<?php

namespace App\Services;

use App\Models\Printers\DevicesPrintersModel;
use RuntimeException;

class NameService
{
  private const PRINTER_TYPE = 3;


  private const LABEL_HEIGHT_MM = 8;

  private const FONT = '1';

  // Fuente TSPL2 número 1: 8 x 12 puntos.
  private const FONT_WIDTH = 8;

  private const FONT_HEIGHT = 16;

  private const MARGIN_DOTS = 40;

  private const LINE_SPACING = 4;

  private const MAX_LINES = 3;

  public function prepareName(
    string $name,
    int $quantity,
    int $deviceId
  ): array {

    $input = $this->validateInput($name, $quantity);

    $printer = $this->getPrinterConfiguration($deviceId);

    $layout = $this->calculateDimensions(
      $input['name'],
      $printer['printable'],
      $printer['height'],
      $printer['dpi']
    );

    $warnings = $this->generateWarnings(
      $input,
      $layout
    );

    $commands = $this->buildPrintCommands(
      $layout,
      $printer['printable'],
      $printer['height'],
      $quantity
    );

    return [
      'printer' => $printer['system_name'],
      'commands' => base64_encode($commands),
      'warnings' => $warnings,
      'layout' => [
        'lines' => $layout['lines'],
        'font' => self::FONT,
        'width_mm' => $printer['printable'],
        'height_mm' => $printer['height'],
        'dpi' => $printer['dpi'],
      ],
    ];
  }

  // ============================================================
  // VALIDAR ENTRADA
  // ============================================================

  private function validateInput(
    string $name,
    int $quantity
  ): array {

    $name = trim($name);

    if ($name === '') {
      throw new RuntimeException(
        'EL NOMBRE DEL PRODUCTO ESTA VACIO.'
      );
    }

    if ($quantity < 1) {
      throw new RuntimeException(
        'LA CANTIDAD DE IMPRESION NO ES VALIDA.'
      );
    }

    // Evitar caracteres que puedan alterar comandos TSPL2.
    $name = str_replace(
      ["\r", "\n", '"'],
      ['', ' ', "'"],
      $name
    );

    $name = preg_replace('/\s+/', ' ', $name);

    return [
      'name' => trim($name),
      'warnings' => [],
    ];
  }

  // ============================================================
  // CONFIGURACION DE IMPRESORA
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
        'NO HAY UNA IMPRESORA DE ETIQUETAS CONFIGURADA.'
      );
    }

    $systemName = trim(
      (string) ($printer['printer_system_name'] ?? '')
    );

    $printable = (float) ($printer['printable'] ?? 0);

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
        'LA IMPRESORA NO ESTA CONFIGURADA PARA QZ TRAY.'
      );
    }

    if ($printable <= 0) {
      throw new RuntimeException(
        'EL ANCHO IMPRIMIBLE DE LA ETIQUETA NO ES VALIDO.'
      );
    }

    if ($height <= 0) {
      throw new RuntimeException(
        'EL ALTO DE LA ETIQUETA NO ESTA CONFIGURADO.'
      );
    }

    if ($dpi <= 0) {
      throw new RuntimeException(
        'EL DPI DE LA IMPRESORA NO ESTA CONFIGURADO.'
      );
    }

    return [
      'system_name' => $systemName,
      'printable' => $printable,
      'height' => $height,
      'dpi' => $dpi,
    ];
  }

  // ============================================================
  // CALCULAR DISTRIBUCION DEL TEXTO
  // ============================================================

  private function calculateDimensions(
    string $name,
    float $widthMM,
    float $heightMM,
    int $dpi
  ): array {

    $dotsPerMM = $dpi / 25.4;

    $widthDots = (int) floor(
      $widthMM * $dotsPerMM
    );

    $availableDots = $widthDots - self::MARGIN_DOTS;

    $maxCharacters = (int) floor(
      $availableDots / (self::FONT_WIDTH + 1)
    );

    if ($maxCharacters < 1) {
      throw new RuntimeException(
        'EL ANCHO DE LA ETIQUETA ES DEMASIADO PEQUEÑO.'
      );
    }

    $lines = $this->wrapText(
      $name,
      $maxCharacters
    );

    $totalHeight = count($lines) * self::FONT_HEIGHT
      + (count($lines) - 1) * self::LINE_SPACING;

    $labelHeightDots = (int) floor(
      $heightMM * $dotsPerMM
    );

    $startY = (int) floor(
      ($labelHeightDots - $totalHeight) / 2
    );

    $startY = max(0, $startY);

    $positions = [];

    foreach ($lines as $index => $line) {

      $characters = mb_strlen($line, 'UTF-8');

      $textWidth = $characters * (self::FONT_WIDTH + 1);

      $x = (int) floor(
        ($widthDots - $textWidth) / 2
      );

      $y = $startY + (
        $index * (
          self::FONT_HEIGHT + self::LINE_SPACING
        )
      );

      $positions[] = [
        'text' => $line,
        'x' => max(0, $x),
        'y' => $y,
      ];
    }

    return [
      'lines' => $lines,
      'positions' => $positions,
      'max_characters' => $maxCharacters,
      'width_dots' => $widthDots,
      'total_height' => $totalHeight,
      'label_height_dots' => $labelHeightDots,
    ];
  }

  // ============================================================
  // DIVIDIR TEXTO EN LINEAS
  // ============================================================

  private function wrapText(
    string $text,
    int $maxCharacters
  ): array {

    $words = preg_split(
      '/\s+/u',
      trim($text),
      -1,
      PREG_SPLIT_NO_EMPTY
    );

    $lines = [];

    $currentLine = '';

    foreach ($words as $word) {

      $candidate = $currentLine === ''
        ? $word
        : $currentLine . ' ' . $word;

      if (
        mb_strlen($candidate, 'UTF-8')
        <= $maxCharacters
      ) {
        $currentLine = $candidate;
        continue;
      }

      if ($currentLine !== '') {
        $lines[] = $currentLine;
      }

      // Si una palabra supera el ancho disponible,
      // se divide para evitar que quede fuera de la etiqueta.
      while (
        mb_strlen($word, 'UTF-8')
        > $maxCharacters
      ) {

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

      $currentLine = $word;
    }

    if ($currentLine !== '') {
      $lines[] = $currentLine;
    }

    return $lines;
  }

  // ============================================================
  // GENERAR COMANDOS DE TEXTO
  // ============================================================

  private function generateTextCommand(
    array $position
  ): string {

    $text = str_replace(
      '"',
      "'",
      $position['text']
    );

    return sprintf(
      'TEXT %d,%d,"%s",0,1,1,"%s"',
      $position['x'],
      $position['y'],
      self::FONT,
      $text
    );
  }

  // ============================================================
  // CONSTRUIR COMANDOS TSPL2
  // ============================================================

  private function buildPrintCommands(
    array $layout,
    float $widthMM,
    float $heightMM,
    int $quantity
  ): string {

    $commands = [];

    $commands[] = sprintf(
      'SIZE %.2f mm,%.2f mm',
      $widthMM,
      $heightMM
    );

    $commands[] = 'GAP 2 mm,0 mm';

    $commands[] = 'CLS';

    foreach ($layout['positions'] as $position) {

      $commands[] = $this->generateTextCommand(
        $position
      );
    }

    $commands[] = sprintf(
      'PRINT 1,%d',
      $quantity
    );

    return implode("\r\n", $commands) . "\r\n";
  }

  // ============================================================
  // ADVERTENCIAS
  // ============================================================

  private function generateWarnings(
    array $input,
    array $layout
  ): array {

    $warnings = [];

    if (count($layout['lines']) > self::MAX_LINES) {

      $warnings[] =
        'EL NOMBRE ES MUY LARGO. SE DIVIDIO EN '
        . count($layout['lines'])
        . ' LINEAS.';
    }

    if (
      $layout['total_height']
      > $layout['label_height_dots']
    ) {

      $warnings[] =
        'EL TEXTO SUPERA LA ALTURA DISPONIBLE DE LA ETIQUETA.';
    }
    if (
      $layout['total_height']
      > $layout['label_height_dots']
    ) {
      $warnings[] =
        'EL TEXTO SUPERA LA ALTURA DISPONIBLE DE LA ETIQUETA. '
        . 'PODRIA QUEDAR CORTADO.';
    }

    return $warnings;
  }
}
