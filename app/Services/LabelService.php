<?php

namespace App\Services;

use App\Models\Printers\DevicesPrintersModel;
use RuntimeException;

class LabelService
{
  /**
   * Tipo de impresora:
   * 6 = Etiqueta
   */
  private const PRINTER_TYPE = 6;

  /**
   * Configuración del diseño.
   */
  private const FONT = '1';
  private const FONT_WIDTH = 16;
  private const FONT_HEIGHT = 24;

  private const MAX_DESCRIPTION_LINES = 2;

  private DevicesPrintersModel $printersModel;

  public function __construct()
  {
    $this->printersModel = new DevicesPrintersModel();
  }

  /**
   * Prepara la etiqueta para impresión.
   */
  public function prepareLabel(
    string $code,
    string $description,
    string $priceOne,
    string $priceTwo,
    string $cantTwo,
    int $quantity,
    int $deviceId
  ): array {

    // =====================================================
    // 1. VALIDACIONES
    // =====================================================

    $code = trim($code);
    $description = trim($description);

    if ($code === '') {
      throw new RuntimeException(
        'EL CÓDIGO DEL PRODUCTO ES OBLIGATORIO.'
      );
    }

    if ($description === '') {
      throw new RuntimeException(
        'LA DESCRIPCIÓN DEL PRODUCTO ES OBLIGATORIA.'
      );
    }

    if ($quantity <= 0) {
      throw new RuntimeException(
        'LA CANTIDAD DE ETIQUETAS DEBE SER MAYOR A CERO.'
      );
    }

    if ($deviceId <= 0) {
      throw new RuntimeException(
        'NO SE ENCONTRÓ EL DISPOSITIVO ACTUAL.'
      );
    }

    // =====================================================
    // 2. CONFIGURACIÓN DE IMPRESORA
    // =====================================================

    $printer = $this->getPrinterConfiguration($deviceId);

    // =====================================================
    // 3. CALCULAR DIMENSIONES
    // =====================================================

    $layout = $this->calculateDimensions(
      $code,
      $description,
      $priceOne,
      $priceTwo,
      $cantTwo,
      $printer['width_mm'],
      $printer['height_mm'],
      $printer['dpi']
    );

    // =====================================================
    // 4. GENERAR COMANDOS TSPL
    // =====================================================

    $commands = $this->buildPrintCommands(
      $code,
      $priceOne,
      $priceTwo,
      $cantTwo,
      $layout,
      $printer,
      $quantity
    );

    // =====================================================
    // 5. RESPUESTA
    // =====================================================

    return [
      'printer' => [
        'printer' => $printer['system_name'],
        'printer_id' => $printer['printer_id'],
        'width_mm' => $printer['width_mm'],
        'height_mm' => $printer['height_mm'],
        'dpi' => $printer['dpi'],
      ],

      'commands' => base64_encode($commands),

      'quantity' => $quantity,

      'layout' => $layout,
    ];
  }

  /**
   * Obtiene la impresora correspondiente al dispositivo.
   */
  private function getPrinterConfiguration(int $deviceId): array
  {
    $printer = $this->printersModel->getPrinterForDevice(
      $deviceId,
      self::PRINTER_TYPE
    );

    if (!$printer) {
      throw new RuntimeException(
        'NO HAY UNA IMPRESORA DE ETIQUETAS CONFIGURADA PARA ESTE DISPOSITIVO.'
      );
    }

    $systemName = trim(
      $printer['printer_system_name'] ?? ''
    );

    $width = (float) ($printer['printable'] ?? 0);

    $height = (float) ($printer['height'] ?? 0);

    $dpi = (int) (
      $printer['printer_dpi']
      ?? $printer['dpi']
      ?? 0
    );

    $driver = (int) ($printer['printer_driver'] ?? 0);

    if ($systemName === '') {
      throw new RuntimeException(
        'LA IMPRESORA NO TIENE UN NOMBRE DE SISTEMA CONFIGURADO.'
      );
    }

    if ($driver !== 1) {
      throw new RuntimeException(
        'LA IMPRESORA CONFIGURADA NO UTILIZA QZ TRAY.'
      );
    }

    if ($width <= 0 || $height <= 0) {
      throw new RuntimeException(
        'EL ANCHO O ALTO DE LA ETIQUETA NO ESTÁ CONFIGURADO CORRECTAMENTE.'
      );
    }

    if ($dpi <= 0) {
      throw new RuntimeException(
        'LA RESOLUCIÓN DPI DE LA IMPRESORA NO ESTÁ CONFIGURADA.'
      );
    }

    return [
      'printer_id' => (int) $printer['printer_id'],
      'system_name' => $systemName,
      'width_mm' => $width,
      'height_mm' => $height,
      'dpi' => $dpi,
      'dots_per_mm' => $dpi / 25.4,
    ];
  }

  /**
   * Calcula las posiciones de los elementos de la etiqueta.
   */
  private function calculateDimensions(
    string $code,
    string $description,
    string $priceOne,
    string $priceTwo,
    string $cantTwo,
    float $widthMM,
    float $heightMM,
    int $dpi
  ): array {

    $dotsPerMM = $dpi / 25.4;

    $widthDots = (int) floor($widthMM * $dotsPerMM);
    $heightDots = (int) floor($heightMM * $dotsPerMM);

    $margin = (int) ceil(1.5 * $dotsPerMM);

    if ($widthDots <= 0 || $heightDots <= 0) {
      throw new RuntimeException(
        'LAS DIMENSIONES DE LA ETIQUETA NO SON VÁLIDAS.'
      );
    }

    // =====================================================
    // ANCHO REAL DISPONIBLE
    // =====================================================

    $availableWidth = $widthDots - ($margin * 2);

    if ($availableWidth <= 0) {
      throw new RuntimeException(
        'EL ANCHO IMPRIMIBLE DE LA ETIQUETA ES INSUFICIENTE.'
      );
    }

    // =====================================================
    // DESCRIPCIÓN
    // FUENTE 1 - ESCALA 2X2
    // ANCHO ESTIMADO: 16 DOTS POR CARÁCTER
    // =====================================================

    $descriptionFontWidth = 16;

    $maxCharacters = max(
      1,
      (int) floor($availableWidth / $descriptionFontWidth)
    );

    $lines = $this->wrapText(
      $description,
      $maxCharacters
    );

    $lines = array_slice(
      $lines,
      0,
      self::MAX_DESCRIPTION_LINES
    );

    $descriptionY = $margin;
    $descriptionLineHeight = 26;

    $descriptionPositions = [];

    foreach ($lines as $index => $line) {

      // Garantizar que ninguna línea supere el ancho disponible.
      $line = $this->truncateText(
        $line,
        $maxCharacters
      );

      $textWidth = mb_strlen(
        $line,
        'UTF-8'
      ) * $descriptionFontWidth;

      // Centrado horizontal.
      $x = (int) floor(
        ($widthDots - $textWidth) / 2
      ) - (int) round(10 * $dotsPerMM);

      // Limitar posición dentro de los márgenes.
      $maxX = $widthDots - $margin - $textWidth;

      $x = max(
        $margin,
        min($x, $maxX)
      );

      $descriptionPositions[] = [
        'x' => $x,
        'y' => $descriptionY + ($index * $descriptionLineHeight),
        'text' => $line,
      ];
    }

    $descriptionEndY = $descriptionY
      + (count($lines) * $descriptionLineHeight);

    // =====================================================
    // CÓDIGO DEL PRODUCTO
    // FUENTE 2 - ESCALA 1X1
    // ANCHO ESTIMADO: 12 DOTS POR CARÁCTER
    // =====================================================

    $codeFontWidth = 12;

    $maxCodeCharacters = max(
      1,
      (int) floor($availableWidth / $codeFontWidth)
    );

    $code = $this->truncateText(
      $code,
      $maxCodeCharacters
    );

    $codeWidth = mb_strlen(
      $code,
      'UTF-8'
    ) * $codeFontWidth;

    $codeY = $descriptionEndY + 2;

    // Centrado horizontal del código.
    $codeX = (int) floor(
      ($widthDots - $codeWidth) / 2
    ) - (int) round(1 * $dotsPerMM);

    // Limitar posición dentro de los márgenes.
    $maxCodeX = $widthDots - $margin - $codeWidth;

    $codeX = max(
      $margin,
      min($codeX, $maxCodeX)
    );

    // =====================================================
    // PRECIOS
    // =====================================================

    // Separación adicional respecto al código.
    $priceY = $codeY + 20 + 8;

    // Dividir la etiqueta en dos mitades.
    $halfWidth = (int) floor($widthDots / 2);

    // =====================================================
    // PRECIO 1
    // FUENTE 2 - ESCALA 2X2
    // ANCHO ESTIMADO: 24 DOTS POR CARÁCTER
    // =====================================================

    $priceOneMaxCharacters = max(
      1,
      (int) floor(
        ($halfWidth - ($margin * 2)) / 24
      )
    );

    $priceOneText = $this->truncateText(
      $this->cleanText($priceOne),
      $priceOneMaxCharacters
    );

    $priceOneWidth = mb_strlen(
      $priceOneText,
      'UTF-8'
    ) * 24;

    $priceOneX = (int) floor(
      ($halfWidth - $priceOneWidth) / 2
    );

    $maxPriceOneX = $halfWidth - $margin - $priceOneWidth;

    $priceOneX = max(
      $margin,
      min($priceOneX, $maxPriceOneX)
    );

    // =====================================================
    // PRECIO 2 + CANTIDAD
    // FUENTE 2 - ESCALA 1X1
    // =====================================================

    $priceTwoText = $this->cleanText(
      $priceTwo . ' x' . $cantTwo
    );

    $priceTwoMaxCharacters = max(
      1,
      (int) floor(
        ($halfWidth - ($margin * 2)) / 12
      )
    );

    $priceTwoText = $this->truncateText(
      $priceTwoText,
      $priceTwoMaxCharacters
    );

    $priceTwoWidth = mb_strlen(
      $priceTwoText,
      'UTF-8'
    ) * 12;

    // Centrar dentro de la mitad derecha.
    $priceTwoX = $halfWidth + (int) floor(
      ($halfWidth - $priceTwoWidth) / 2
    );

    $minPriceTwoX = $halfWidth + $margin;
    $maxPriceTwoX = $widthDots - $margin - $priceTwoWidth;

    $priceTwoX = max(
      $minPriceTwoX,
      min($priceTwoX, $maxPriceTwoX)
    );

    // Centrado vertical respecto al precio 1.
    $priceTwoY = $priceY + 10;

    // =====================================================
    // VALIDACIÓN DE ALTURA
    // =====================================================

    $minimumHeight = $priceY + 40 + $margin;

    if ($minimumHeight > $heightDots) {
      throw new RuntimeException(
        'LA ALTURA CONFIGURADA NO ES SUFICIENTE PARA ESTE DISEÑO DE ETIQUETA.'
      );
    }

    // =====================================================
    // RESULTADO
    // =====================================================

    return [
      'width_dots' => $widthDots,
      'height_dots' => $heightDots,

      'description_lines' => $lines,
      'description_positions' => $descriptionPositions,

      'code_x' => $codeX,
      'code_y' => $codeY,

      'price_one_x' => $priceOneX,
      'price_two_x' => $priceTwoX,

      'price_y' => $priceY,
      'price_two_y' => $priceTwoY,

      'price_one_text' => $priceOneText,
      'price_two_text' => $priceTwoText,

      'margin' => $margin,
      'dots_per_mm' => $dotsPerMM,
    ];
  }
  /**
   * Genera los comandos TSPL.
   */
  private function buildPrintCommands(
    string $code,
    string $priceOne,
    string $priceTwo,
    string $cantTwo,
    array $layout,
    array $printer,
    int $quantity
  ): string {

    $width = number_format(
      $printer['width_mm'],
      2,
      '.',
      ''
    );

    $height = number_format(
      $printer['height_mm'],
      2,
      '.',
      ''
    );

    $commands = [];

    $commands[] = 'SIZE ' . $width . ' mm,' . $height . ' mm';

    $commands[] = 'GAP 2 mm,0 mm';

    $commands[] = 'DIRECTION 1';

    $commands[] = 'REFERENCE 0,0';

    $commands[] = 'CLS';

    // =====================================================
    // DESCRIPCIÓN CENTRADA
    // FUENTE 1 - ESCALA 2X2
    // =====================================================

    foreach ($layout['description_positions'] as $position) {

      $commands[] = sprintf(
        'TEXT %d,%d,"1",0,2,2,"%s"',
        $position['x'],
        $position['y'],
        $this->escapeTSPL($position['text'])
      );
    }

    // =====================================================
    // CÓDIGO CENTRADO
    // FUENTE 2 - ESCALA 1X1
    // =====================================================

    $commands[] = sprintf(
      'TEXT %d,%d,"2",0,1,1,"%s"',
      $layout['code_x'],
      $layout['code_y'],
      $this->escapeTSPL($code)
    );

    // =====================================================
    // PRECIO 1
    // FUENTE 2 - ESCALA 2X2
    // =====================================================

    $commands[] = sprintf(
      'TEXT %d,%d,"2",0,2,2,"%s"',
      $layout['price_one_x'],
      $layout['price_y'],
      $this->escapeTSPL($layout['price_one_text'])
    );

    // =====================================================
    // PRECIO 2 + CANTIDAD
    // FUENTE 2 - ESCALA 1X1
    // =====================================================

    $commands[] = sprintf(
      'TEXT %d,%d,"2",0,1,1,"%s"',
      $layout['price_two_x'],
      $layout['price_two_y'],
      $this->escapeTSPL($layout['price_two_text'])
    );

    // =====================================================
    // CANTIDAD DE ETIQUETAS
    // =====================================================

    $commands[] = 'PRINT 1,' . $quantity;

    return implode("\r\n", $commands) . "\r\n";
  }

  /**
   * Divide la descripción en líneas.
   */
  private function wrapText(
    string $text,
    int $maxCharacters
  ): array {

    $text = $this->cleanText($text);

    $words = preg_split(
      '/\s+/u',
      $text,
      -1,
      PREG_SPLIT_NO_EMPTY
    );

    $lines = [];
    $currentLine = '';

    foreach ($words as $word) {

      if (mb_strlen($word, 'UTF-8') > $maxCharacters) {

        if ($currentLine !== '') {
          $lines[] = $currentLine;
          $currentLine = '';
        }

        while (mb_strlen($word, 'UTF-8') > $maxCharacters) {

          $lines[] = mb_substr(
            $word,
            0,
            $maxCharacters,
            'UTF-8'
          );

          $word = mb_substr(
            $word,
            $maxCharacters,
            null,
            'UTF-8'
          );
        }
      }

      $candidate = $currentLine === ''
        ? $word
        : $currentLine . ' ' . $word;

      if (mb_strlen($candidate, 'UTF-8') <= $maxCharacters) {
        $currentLine = $candidate;
      } else {

        if ($currentLine !== '') {
          $lines[] = $currentLine;
        }

        $currentLine = $word;
      }
    }

    if ($currentLine !== '') {
      $lines[] = $currentLine;
    }

    return $lines;
  }

  /**
   * Limpia textos.
   */
  private function cleanText(string $text): string
  {
    $text = str_replace(
      ["\r", "\n", '"'],
      [' ', ' ', "'"],
      $text
    );

    return trim(
      preg_replace('/\s+/u', ' ', $text) ?? ''
    );
  }

  /**
   * Evita que un texto supere el espacio disponible.
   */
  private function truncateText(
    string $text,
    int $maxCharacters
  ): string {

    if (mb_strlen($text, 'UTF-8') <= $maxCharacters) {
      return $text;
    }

    return mb_substr(
      $text,
      0,
      max(1, $maxCharacters - 1),
      'UTF-8'
    ) . '.';
  }

  /**
   * Escapa texto para comandos TSPL.
   */
  private function escapeTSPL(string $text): string
  {
    return str_replace(
      '"',
      "'",
      $text
    );
  }
}