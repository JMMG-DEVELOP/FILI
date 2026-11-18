<?php

namespace App\Services;

use App\Models\Printers\DevicesPrintersModel;
use RuntimeException;

class CodeService
{
  // ============================================================
  // CONFIGURACIÓN GENERAL
  // ============================================================

  private const PRINTER_TYPE = 3;

  // private const SUPPORTED_DPI = 203;

  private const DEFAULT_HEIGHT_MM = 8.00;

  private const BARCODE_HEIGHT_MAX = 40;

  private const TEXT_FONT = '1';

  private const TEXT_FONT_WIDTH = 8;

  private const TEXT_FONT_HEIGHT = 12;

  // ============================================================
  // 1. PROCESO PRINCIPAL
  // ============================================================

  public function prepareBarcode(
    string $code,
    int $quantity,
    int $deviceId
  ): array {

    // Validar datos recibidos.
    $input = $this->validateInput(
      $code,
      $quantity,
      $deviceId
    );

    $code = $input['code'];

    // Obtener configuración de impresora.
    $printer = $this->getPrinterConfiguration(
      $deviceId
    );

    // Detectar simbología.
    $barcode = $this->detectBarcodeType(
      $code
    );

    // Calcular dimensiones.
    $dimensions = $this->calculateBarcodeDimensions(
      $barcode,
      $printer
    );

    // Generar comandos de barras.
    $barcodeCommand = $this->generateBarcodeCommand(
      $barcode,
      $dimensions
    );

    // Generar texto inferior.
    $textCommand = $this->generateTextCommand(
      $code,
      $dimensions
    );

    // Construir trabajo TSPL2.
    $commands = $this->buildPrintCommands(
      $printer,
      $barcodeCommand,
      $textCommand,
      $quantity
    );

    // Generar advertencias.
    $warnings = $this->generateWarnings(
      $barcode,
      $dimensions,
      $input
    );

    return [

      'code' => $code,

      'quantity' => $quantity,

      'printer' => $printer['name'],

      'printer_id' => $printer['id'],

      'printable' => $printer['width_mm'],

      'width_dots' => $dimensions['width_dots'],

      'dpi' => $printer['dpi'],

      'height' => $dimensions['height_mm'],

      'format' => $barcode['type'],

      'barcode_type' => $barcode['type'],

      'language' => 'TSPL2',

      'warnings' => $warnings,

      'is_readable_estimate' => $dimensions['readable_estimate'],

      'dimensions' => $dimensions,

      'commands' => base64_encode($commands)

    ];
  }

  // ============================================================
  // 2. VALIDAR DATOS DE ENTRADA
  // ============================================================

  private function validateInput(
    string $code,
    int $quantity,
    int $deviceId
  ): array {

    $code = trim($code);

    if ($code === '') {
      throw new RuntimeException(
        'EL CÓDIGO DEL PRODUCTO ES OBLIGATORIO'
      );
    }

    if ($quantity < 1 || $quantity > 500) {
      throw new RuntimeException(
        'LA CANTIDAD DEBE ESTAR ENTRE 1 Y 500'
      );
    }

    if ($deviceId <= 0) {
      throw new RuntimeException(
        'NO SE ENCONTRÓ EL DISPOSITIVO ACTUAL'
      );
    }

    $warnings = [];

    // Eliminar caracteres de control que pueden interferir
    // con los comandos TSPL2.
    $cleanCode = preg_replace(
      '/[\x00-\x1F\x7F]/',
      '',
      $code
    );

    if ($cleanCode !== $code) {
      $warnings[] =
        'EL CÓDIGO CONTENÍA CARACTERES DE CONTROL. '
        . 'SE ELIMINARON PARA PREPARAR LA IMPRESIÓN.';
    }

    // Las comillas pueden alterar la sintaxis TSPL.
    if (str_contains($cleanCode, '"')) {

      $cleanCode = str_replace(
        '"',
        '',
        $cleanCode
      );

      $warnings[] =
        'EL CÓDIGO CONTENÍA COMILLAS. '
        . 'SE ELIMINARON PARA EVITAR ERRORES TSPL2.';
    }

    if ($cleanCode === '') {
      throw new RuntimeException(
        'EL CÓDIGO NO CONTIENE CARACTERES IMPRIMIBLES'
      );
    }

    // CODE128 trabaja con caracteres ASCII.
    if (preg_match('/[^\x20-\x7E]/', $cleanCode)) {
      $warnings[] =
        'EL CÓDIGO CONTIENE CARACTERES FUERA DEL RANGO ASCII. '
        . 'SU LECTURA PUEDE NO SER COMPATIBLE CON CODE128.';
    }

    return [

      'code' => $cleanCode,

      'warnings' => $warnings

    ];
  }

  // ============================================================
  // 3. OBTENER CONFIGURACIÓN DE IMPRESORA
  // ============================================================

  private function getPrinterConfiguration(
    int $deviceId
  ): array {

    $model = new DevicesPrintersModel();

    $printer = $model->getPrinterForDevice(
      $deviceId,
      self::PRINTER_TYPE
    );

    if (!$printer) {
      throw new RuntimeException(
        'NO HAY UNA IMPRESORA DE CÓDIGOS ASIGNADA'
      );
    }

    $name = trim(
      (string) ($printer['printer_system_name'] ?? '')
    );

    $id = (int) ($printer['printer_id'] ?? 0);

    $width = (float) ($printer['printable'] ?? 0);
    $height = (float) ($printer['height'] ?? 0);
    $dpi = (int) (
      $printer['printer_dpi']
      ?? $printer['dpi']
      ?? 0
    );

    $driver = (int) ($printer['printer_driver'] ?? 0);

    if ($name === '') {
      throw new RuntimeException(
        'NO SE RECIBIÓ EL NOMBRE DE LA IMPRESORA'
      );
    }

    if ($driver !== 1) {
      throw new RuntimeException(
        'LA IMPRESORA NO TIENE CONFIGURADO QZ TRAY'
      );
    }

    if ($width <= 0) {
      throw new RuntimeException(
        'EL ANCHO IMPRIMIBLE NO ESTÁ CONFIGURADO'
      );
    }
    if ($height <= 0) {
      throw new RuntimeException(
        'EL ALTO DE LA ETIQUETA NO ESTA CONFIGURADO'
      );
    }

    $dpi = (int) (
      $printer['printer_dpi']
      ?? $printer['dpi']
      ?? 0
    );

    if ($dpi <= 0) {
      throw new RuntimeException(
        'EL DPI DE LA IMPRESORA NO ESTA CONFIGURADO.'
      );
    }

    return [

      'name' => $name,

      'id' => $id,

      'width_mm' => $width,

      'dpi' => $dpi,

      'dots_per_mm' => $dpi / 25.4,

      'driver' => $driver,
      'height_mm' => $height,

    ];
  }

  // ============================================================
  // 4. DETECTAR SIMBOLOGÍA DEL CÓDIGO
  // ============================================================

  private function detectBarcodeType(
    string $code
  ): array {

    $warnings = [];

    // Por defecto utilizamos CODE128.
    $type = '128';

    $encodedValue = $code;

    // --------------------------------------------------------
    // EAN-8
    // --------------------------------------------------------

    if (preg_match('/^[0-9]{8}$/', $code)) {

      if ($this->validateEANChecksum($code)) {

        $type = 'EAN8';

        // La impresora calcula el dígito de control.
        $encodedValue = substr($code, 0, 7);

      } else {

        $warnings[] =
          'EL CÓDIGO TIENE 8 DÍGITOS, PERO SU CHECKSUM '
          . 'EAN-8 ES INCORRECTO. SE IMPRIMIRÁ COMO CODE128.';

      }
    }

    // --------------------------------------------------------
    // UPC-A
    // --------------------------------------------------------
    elseif (preg_match('/^[0-9]{12}$/', $code)) {

      if ($this->validateEANChecksum($code)) {

        $type = 'UPCA';

        $encodedValue = substr($code, 0, 11);

      } else {

        $warnings[] =
          'EL CÓDIGO TIENE 12 DÍGITOS, PERO SU CHECKSUM '
          . 'UPC-A ES INCORRECTO. SE IMPRIMIRÁ COMO CODE128.';

      }
    }

    // --------------------------------------------------------
    // EAN-13
    // --------------------------------------------------------
    elseif (preg_match('/^[0-9]{13}$/', $code)) {

      if ($this->validateEANChecksum($code)) {

        $type = 'EAN13';

        $encodedValue = substr($code, 0, 12);

      } else {

        $warnings[] =
          'EL CÓDIGO TIENE 13 DÍGITOS, PERO SU CHECKSUM '
          . 'EAN-13 ES INCORRECTO. SE IMPRIMIRÁ COMO CODE128.';

      }
    }

    // --------------------------------------------------------
    // CÓDIGOS INTERNOS Y OTROS FORMATOS
    // --------------------------------------------------------
    else {

      $type = '128';

      $encodedValue = $code;

      if (preg_match('/^[0-9]+$/', $code)) {

        if (strlen($code) === 7) {

          // Código interno de FILI.
          $type = '128';

        } elseif (strlen($code) === 16) {

          $warnings[] =
            'CÓDIGO NUMÉRICO DE 16 DÍGITOS. '
            . 'NO SE IDENTIFICÓ COMO EAN-8, UPC-A O EAN-13. '
            . 'SE UTILIZARÁ CODE128.';

        } else {

          $warnings[] =
            'FORMATO NUMÉRICO NO IDENTIFICADO. '
            . 'SE UTILIZARÁ CODE128.';

        }

      } else {

        $warnings[] =
          'FORMATO DE CÓDIGO NO IDENTIFICADO. '
          . 'SE UTILIZARÁ CODE128.';

      }
    }

    return [

      'type' => $type,

      'encoded_value' => $encodedValue,

      'original_value' => $code,

      'warnings' => $warnings

    ];
  }

  // ============================================================
  // 5. VALIDAR CHECKSUM EAN / UPC
  // ============================================================

  private function validateEANChecksum(
    string $code
  ): bool {

    if (!preg_match('/^[0-9]+$/', $code)) {
      return false;
    }

    $length = strlen($code);

    if (!in_array($length, [8, 12, 13], true)) {
      return false;
    }

    $payload = substr(
      $code,
      0,
      -1
    );

    $providedCheck = (int) substr(
      $code,
      -1
    );

    $sum = 0;

    // Desde la derecha del contenido, alternar pesos 3 y 1.
    $weight = 3;

    for (
      $i = strlen($payload) - 1;
      $i >= 0;
      $i--
    ) {

      $digit = (int) $payload[$i];

      $sum += $digit * $weight;

      $weight = ($weight === 3) ? 1 : 3;
    }

    $calculatedCheck = (
      10 - ($sum % 10)
    ) % 10;

    return $calculatedCheck === $providedCheck;
  }

  // ============================================================
  // 6. CALCULAR DIMENSIONES DEL CÓDIGO
  // ============================================================

  private function calculateBarcodeDimensions(
    array $barcode,
    array $printer
  ): array {

    $widthMM = $printer['width_mm'];

    $heightMM = $printer['height_mm'];

    $dpi = $printer['dpi'];

    $dotsPerMM = $printer['dots_per_mm'];

    $widthDots = (int) round(
      $widthMM * $dotsPerMM
    );

    $heightDots = (int) round(
      $heightMM * $dotsPerMM
    );

    $marginDots = (int) round(
      $dotsPerMM
    );

    $availableWidth = max(
      1,
      $widthDots - ($marginDots * 2)
    );

    // --------------------------------------------------------
    // MÓDULOS SEGÚN SIMBOLOGÍA
    // --------------------------------------------------------

    switch ($barcode['type']) {

      case 'EAN13':

      case 'UPCA':

        // 95 módulos de barras + 22 de zonas silenciosas.
        $barcodeModules = 95;

        $quietModules = 11;

        break;

      case 'EAN8':

        // 67 módulos de barras + 14 de zonas silenciosas.
        $barcodeModules = 67;

        $quietModules = 7;

        break;

      default:

        // Estimación conservadora para CODE128.
        $barcodeModules = (
          11 * strlen($barcode['encoded_value'])
        ) + 35;

        // Zona silenciosa aproximada a cada lado.
        $quietModules = 10;

        break;
    }

    // --------------------------------------------------------
    // ANCHO DE MÓDULO
    // --------------------------------------------------------

    // Intentar primero con 2 puntos por módulo.
    $moduleWidth = 2;

    $totalModules = $barcodeModules + (
      $quietModules * 2
    );

    $estimatedWidth = $totalModules * $moduleWidth;

    // Si no entra, intentar con 1 punto.
    if ($estimatedWidth > $availableWidth) {

      $moduleWidth = 1;

      $estimatedWidth = $totalModules * $moduleWidth;

    }

    // Si todavía no entra, se imprime igualmente.
    $widthOverflow = $estimatedWidth > $availableWidth;

    // --------------------------------------------------------
    // POSICIÓN HORIZONTAL
    // --------------------------------------------------------

    $totalStartX = (int) floor(
      ($widthDots - $estimatedWidth) / 2
    );

    if ($widthOverflow) {

      $totalStartX = $marginDots;

    }

    $barcodeX = $totalStartX + (
      $quietModules * $moduleWidth
    );

    // No permitir coordenadas negativas.
    $barcodeX = max(
      0,
      $barcodeX
    );

    // --------------------------------------------------------
    // ALTURA DE BARRAS
    // --------------------------------------------------------

    $barcodeHeight = min(
      self::BARCODE_HEIGHT_MAX,
      max(20, $heightDots - 24)
    );

    // Subir las barras para aprovechar el espacio superior.
    $barcodeY = 0;

    // --------------------------------------------------------
    // POSICIÓN DEL TEXTO
    // --------------------------------------------------------

    $textWidth = strlen($barcode['original_value'])
      * self::TEXT_FONT_WIDTH;

    $textX = (int) floor(
      ($widthDots - $textWidth) / 2
    );

    $textX = max(
      0,
      $textX
    );

    $textY = $barcodeY + $barcodeHeight + 2;

    $textOverflow = (
      $textWidth > $widthDots
    );

    // --------------------------------------------------------
    // EVALUACIÓN ESTIMADA
    // --------------------------------------------------------

    $heightWarning = $heightMM < 15;

    $readableEstimate = (
      !$widthOverflow
      && !$textOverflow
      && $moduleWidth >= 2
      && !$heightWarning
    );

    return [

      'width_mm' => $widthMM,

      'height_mm' => $heightMM,

      'width_dots' => $widthDots,

      'height_dots' => $heightDots,

      'available_width' => $availableWidth,

      'module_width' => $moduleWidth,

      'wide_bar' => $moduleWidth * 2,

      'barcode_x' => $barcodeX,

      'barcode_y' => $barcodeY,

      'barcode_height' => $barcodeHeight,

      'estimated_barcode_width' => $estimatedWidth,

      'text_x' => $textX,

      'text_y' => $textY,

      'text_width' => $textWidth,

      'width_overflow' => $widthOverflow,

      'text_overflow' => $textOverflow,

      'height_warning' => $heightWarning,

      'readable_estimate' => $readableEstimate

    ];
  }

  // ============================================================
  // 7. GENERAR COMANDO DE CÓDIGO DE BARRAS
  // ============================================================

  private function generateBarcodeCommand(
    array $barcode,
    array $dimensions
  ): string {

    $value = $this->escapeTSPL(
      $barcode['encoded_value']
    );

    return sprintf(
      'BARCODE %d,%d,"%s",%d,0,0,%d,%d,"%s"',
      $dimensions['barcode_x'],
      $dimensions['barcode_y'],
      $barcode['type'],
      $dimensions['barcode_height'],
      $dimensions['module_width'],
      $dimensions['wide_bar'],
      $value
    );
  }

  // ============================================================
  // 8. GENERAR TEXTO INFERIOR CENTRADO
  // ============================================================

  private function generateTextCommand(
    string $code,
    array $dimensions
  ): string {

    $safeCode = $this->escapeTSPL($code);

    return sprintf(
      'TEXT %d,%d,"%s",0,1,1,"%s"',
      $dimensions['text_x'],
      $dimensions['text_y'],
      self::TEXT_FONT,
      $safeCode
    );
  }

  // ============================================================
  // 9. ESCAPAR CONTENIDO TSPL2
  // ============================================================

  private function escapeTSPL(
    string $value
  ): string {

    // Las comillas se eliminaron durante la validación.
    // Escapar barras invertidas.
    return str_replace(
      '\\',
      '\\\\',
      $value
    );
  }

  // ============================================================
  // 10. CONSTRUIR TRABAJO COMPLETO DE IMPRESIÓN
  // ============================================================

  private function buildPrintCommands(
    array $printer,
    string $barcodeCommand,
    string $textCommand,
    int $quantity
  ): string {

    $width = number_format(
      $printer['width_mm'],
      2,
      '.',
      ''
    );

    $height = number_format(
      self::DEFAULT_HEIGHT_MM,
      2,
      '.',
      ''
    );

    $commands = [];

    // Tamaño físico de etiqueta.
    $commands[] = "SIZE {$width} mm,{$height} mm";

    // Limpiar formato anterior.
    $commands[] = 'CLS';

    // Barras.
    $commands[] = $barcodeCommand;

    // Texto inferior centrado.
    $commands[] = $textCommand;

    // Imprimir cantidad solicitada.
    $commands[] = "PRINT 1,{$quantity}";

    return implode(
      "\r\n",
      $commands
    ) . "\r\n";
  }

  // ============================================================
  // 11. GENERAR ADVERTENCIAS
  // ============================================================

  private function generateWarnings(
    array $barcode,
    array $dimensions,
    array $input
  ): array {

    $warnings = [];

    // Advertencias de limpieza del código.
    $warnings = array_merge(
      $warnings,
      $input['warnings']
    );

    // Advertencias de simbología.
    $warnings = array_merge(
      $warnings,
      $barcode['warnings']
    );

    if ($dimensions['module_width'] < 2) {

      $warnings[] =
        'EL ANCHO DEL MÓDULO SE REDUJO A 1 PUNTO '
        . 'PARA INTENTAR QUE EL CÓDIGO ENTRE EN LA ETIQUETA. '
        . 'PODRÍA RESULTAR DIFÍCIL DE ESCANEAR.';

    }

    if ($dimensions['width_overflow']) {

      $warnings[] =
        'EL CÓDIGO SUPERA EL ANCHO DISPONIBLE. '
        . 'SE INTENTARÁ IMPRIMIR DE TODAS FORMAS, '
        . 'PERO ALGUNAS BARRAS PODRÍAN QUEDAR FUERA DE LA ETIQUETA.';

    }

    if ($dimensions['text_overflow']) {

      $warnings[] =
        'EL TEXTO DEL CÓDIGO ES MÁS ANCHO QUE LA ETIQUETA. '
        . 'PODRÍA QUEDAR CORTADO.';

    }

    if ($dimensions['height_warning']) {

      $warnings[] =
        'LA ALTURA CONFIGURADA ES DE 8 MM. '
        . 'ES INFERIOR A LA ALTURA HABITUAL DE LOS CÓDIGOS EAN. '
        . 'LA LECTURA PODRÍA VERSE AFECTADA.';

    }

    return array_values(
      array_unique($warnings)
    );
  }
}
