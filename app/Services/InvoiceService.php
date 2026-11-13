<?php

namespace App\Services;

use App\Models\Printers\DevicesPrintersModel;
use CodeIgniter\Database\BaseConnection;
use Dompdf\Dompdf;
use Dompdf\Options;

class InvoiceService
{
  private BaseConnection $db;

  public function __construct()
  {
    $this->db = db_connect();
  }

  /*
   * PROCESO PRINCIPAL
   * Coordina la impresora, los datos, el formato y el PDF.
   */
  public function print_invoice_two(
    int $saleId,
    bool $requirePrinter = true,
    int $receiptType = 2
  ): array {
    if ($saleId <= 0) {
      return $this->error('ID DE VENTA NO VÁLIDO');
    }

    if (!class_exists(Dompdf::class)) {
      return $this->error('DOMPDF NO ESTÁ INSTALADO');
    }

    $printer = [
      'status' => true,
      'printer' => null
    ];

    // La impresora solo es obligatoria para la impresión normal.
    if ($requirePrinter) {
      $printer = $this->print_invoice_two_printers();

      if (!$printer['status']) {
        return $printer;
      }
    }

    $sales = $this->print_invoice_two_sales($saleId);

    if (!$sales['status']) {
      return $sales;
    }

    $data = $this->print_invoice_two_formatter(
      $sales['sale'],
      $sales['details']
    );

    $html = $this->print_invoice_two_html($data);
    $pdf = $this->print_invoice_two_pdf($html);

    // $ultimaPagina = $data['pages'][
    //   array_key_last($data['pages'])
    // ];
    $ultimaPagina = $data['pages'][
      array_key_last($data['pages'])
    ];

    $numeroDocumento = html_entity_decode(
      $ultimaPagina['numeroDocumento'],
      ENT_QUOTES,
      'UTF-8'
    );

    if ($receiptType === 1) {
      /*
       * COMPROBANTE INTERNO
       *
       * Actualizar document_sequence del punto de expedición
       * correspondiente a la venta.
       */
      $partes = preg_split('/\s+/', trim($numeroDocumento));

      $expeditionPointId = (int) (
        $sales['sale']['expedition_point'] ?? 0
      );

      if (
        $expeditionPointId <= 0 ||
        count($partes) !== 3 ||
        !ctype_digit($partes[0]) ||
        !ctype_digit($partes[1]) ||
        !ctype_digit($partes[2])
      ) {
        log_message(
          'error',
          'No se pudo actualizar document_sequence. ' .
          'Venta: ' . $saleId .
          ' | Número: ' . $numeroDocumento
        );

      } else {
        $secuenciaExiste = $this->db
          ->table('document_sequence')
          ->where('expedition_point', $expeditionPointId)
          ->countAllResults();

        if ($secuenciaExiste > 0) {
          $actualizado = $this->db
            ->table('document_sequence')
            ->where('expedition_point', $expeditionPointId)
            ->update([
              'last_number' => $partes[2],
            ]);

          if (!$actualizado) {
            log_message(
              'error',
              'Error al actualizar document_sequence. ' .
              'Venta: ' . $saleId .
              ' | Punto: ' . $expeditionPointId .
              ' | Número: ' . $partes[2]
            );
          }
        } else {
          log_message(
            'error',
            'No existe document_sequence para el punto ' .
            $expeditionPointId .
            ' | Venta: ' . $saleId
          );
        }
      }

    } else {
      /*
       * COMPROBANTE FISCAL
       *
       * Mantener el comportamiento actual.
       */
      $invoiceSequenceModel =
        new \App\Models\Box\InvoiceSequenceModel();

      $actualizado =
        $invoiceSequenceModel->update_last_number_from_document(
          $numeroDocumento
        );

      if (!$actualizado) {
        log_message(
          'error',
          'No se pudo actualizar la secuencia de la factura. ' .
          'Venta: ' . $saleId
        );
      }
    }

    $invoiceSequenceModel = new \App\Models\Box\InvoiceSequenceModel();

    $actualizado = $invoiceSequenceModel->update_last_number_from_document(
      html_entity_decode(
        $ultimaPagina['numeroDocumento'],
        ENT_QUOTES,
        'UTF-8'
      )
    );

    if (!$actualizado) {
      log_message(
        'error',
        'No se pudo actualizar la secuencia de la factura.'
      );
    }

    return [
      'status' => true,
      'filename' => 'factura_' . $saleId . '.pdf',
      'pdf' => base64_encode($pdf),
      'printer' => $printer['printer']
    ];
  }

  /*
   * IMPRESORA
   * Tipo 1 = Ticket
   * Tipo 2 = Factura
   */
  public function print_invoice_two_printers(): array
  {
    $deviceId = (int) session('device');

    if ($deviceId <= 0) {
      return $this->error(
        'NO SE ENCONTRÓ EL DISPOSITIVO DE LA SESIÓN'
      );
    }

    $model = new DevicesPrintersModel();

    $printer = $model->getPrinterForDevice($deviceId, 2);

    if (!$printer) {
      return $this->error(
        'NO HAY UNA IMPRESORA DE FACTURA CONFIGURADA PARA ESTE DISPOSITIVO'
      );
    }

    if (empty($printer['printer_system_name'])) {
      return $this->error(
        'LA IMPRESORA DE FACTURA NO TIENE NOMBRE DE SISTEMA CONFIGURADO'
      );
    }

    return [
      'status' => true,
      'printer' => $printer
    ];
  }

  /*
   * DATOS DE LA VENTA Y SUS PRODUCTOS
   */
  public function print_invoice_two_sales(int $saleId): array
  {
    $sale = $this->db->table('sales s')
      ->select([
        's.id',
        's.number',
        's.date',
        's.time',
        's.type AS sale_type',
        's.total_price',
        's.customer',
        's.sucursal',
        's.expedition_point',
        'c.name AS customer_name',
        'c.ci AS customer_ci',
        'c.address AS customer_address',
        'ep.code AS expedition_point_code',
        'su.name AS sucursal_name'
      ])
      ->join('customers c', 'c.id = s.customer', 'left')
      ->join(
        'expedition_point ep',
        'ep.id = s.expedition_point',
        'left'
      )
      ->join('sucursals su', 'su.id = s.sucursal', 'left')
      ->where('s.id', $saleId)
      ->get()
      ->getRowArray();

    if (!$sale) {
      return $this->error('NO SE ENCONTRÓ LA VENTA');
    }

    $details = $this->db->table('sales_details sd')
      ->select([
        'sd.product',
        'sd.cant',
        'sd.unit_price',
        'sd.total_price',
        'sd.type_iva',
        'sd.total_iva',
        'p.description AS product_description'
      ])
      ->join('products p', 'p.code = sd.product', 'left')
      ->where('sd.sales', $saleId)
      ->get()
      ->getResultArray();

    if (empty($details)) {
      return $this->error('LA VENTA NO TIENE PRODUCTOS');
    }

    return [
      'status' => true,
      'sale' => $sale,
      'details' => $details
    ];
  }

  /*
   * FORMATEO DE DATOS Y CÁLCULOS
   */



  public function print_invoice_two_formatter(
    array $sale,
    array $details
  ): array {
    $escape = static function ($value): string {
      return htmlspecialchars(
        (string) ($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
      );
    };

    $money = static function ($value): string {
      return number_format((float) $value, 0, ',', '.');
    };

    $meses = [
      1 => 'enero',
      2 => 'febrero',
      3 => 'marzo',
      4 => 'abril',
      5 => 'mayo',
      6 => 'junio',
      7 => 'julio',
      8 => 'agosto',
      9 => 'septiembre',
      10 => 'octubre',
      11 => 'noviembre',
      12 => 'diciembre'
    ];

    $fecha = new \DateTime(
      $sale['date'] . ' ' . $sale['time']
    );

    $fechaTexto = $fecha->format('d') . '-' .
      $meses[(int) $fecha->format('n')] . '-' .
      $fecha->format('Y');

    $condicionVenta = (int) ($sale['sale_type'] ?? 1) === 2
      ? 'Crédito'
      : 'Contado';

    $cliente = $escape($sale['customer_name'] ?? 'SIN NOMBRE');
    $ruc = $escape($sale['customer_ci'] ?? '');
    $direccion = $escape($sale['customer_address'] ?? '');

    // Separar los artículos en grupos de máximo 12.
    $grupos = array_chunk(array_values($details), 12);

    // Evitar un documento sin páginas.
    if (empty($grupos)) {
      $grupos = [[]];
    }

    // Preparar el número interno: 001 001 0000047.
    $numeroBase = trim((string) ($sale['number'] ?? ''));

    $partesNumero = preg_split('/\s+/', $numeroBase);

    if (
      count($partesNumero) !== 3 ||
      !ctype_digit($partesNumero[0]) ||
      !ctype_digit($partesNumero[1]) ||
      !ctype_digit($partesNumero[2])
    ) {
      throw new \DomainException(
        'EL NÚMERO INTERNO DE LA VENTA NO ES VÁLIDO'
      );
    }

    $prefijoNumero = $partesNumero[0] . ' ' . $partesNumero[1];
    $secuenciaBase = (int) $partesNumero[2];
    $digitosSecuencia = strlen($partesNumero[2]);

    $pages = [];
    $totalPaginas = count($grupos);

    foreach ($grupos as $indice => $articulos) {
      $exenta = 0.0;
      $total5 = 0.0;
      $total10 = 0.0;
      $total = 0.0;
      $itemsHtml = '';

      // Cada hoja incrementa en uno el número interno.
      $numeroDocumento = $prefijoNumero . ' ' .
        str_pad(
          (string) ($secuenciaBase + $indice),
          $digitosSecuencia,
          '0',
          STR_PAD_LEFT
        );

      foreach ($articulos as $item) {
        $tipoIva = (int) ($item['type_iva'] ?? 3);
        $importe = (float) ($item['total_price'] ?? 0);

        if (!in_array($tipoIva, [1, 2, 3], true)) {
          throw new \DomainException(
            'LA VENTA CONTIENE UN TIPO DE IVA NO VÁLIDO.'
          );
        }

        $total += $importe;

        if ($tipoIva === 3) {
          $exenta += $importe;
        } elseif ($tipoIva === 2) {
          $total5 += $importe;
        } else {
          $total10 += $importe;
        }

        $cantidad = rtrim(
          rtrim(
            number_format(
              (float) ($item['cant'] ?? 0),
              3,
              ',',
              '.'
            ),
            '0'
          ),
          ','
        );

        $codigo = $escape($item['product'] ?? '');

        $descripcion = $escape(
          ($item['product_description'] ?? '') ?:
          ($item['product'] ?? '')
        );

        $itemsHtml .= '
                <tr>
                    <td class="codigo">' . $codigo . '</td>
                    <td class="cantidad">' . $cantidad . '</td>
                    <td class="articulo">' . $descripcion . '</td>
                    <td class="numero">' .
          $money($item['unit_price'] ?? 0) .
          '</td>
                    <td class="numero">' .
          ($tipoIva === 3 ? $money($importe) : '') .
          '</td>
                    <td class="numero">' .
          ($tipoIva === 2 ? $money($importe) : '') .
          '</td>
                    <td class="numero">' .
          ($tipoIva === 1 ? $money($importe) : '') .
          '</td>
                </tr>';
      }

      // Completar hasta 12 filas por hoja.
      $filasVacias = 12 - count($articulos);

      $itemsHtml .= str_repeat(
        '<tr>
                <td class="codigo">&nbsp;</td>
                <td class="cantidad">&nbsp;</td>
                <td class="articulo">&nbsp;</td>
                <td class="numero">&nbsp;</td>
                <td class="numero">&nbsp;</td>
                <td class="numero">&nbsp;</td>
                <td class="numero">&nbsp;</td>
            </tr>',
        $filasVacias
      );

      // Liquidación del IVA correspondiente a esta hoja.
      $iva5 = round($total5 / 21, 0, PHP_ROUND_HALF_UP);
      $iva10 = round($total10 / 11, 0, PHP_ROUND_HALF_UP);
      $totalIva = $iva5 + $iva10;

      $pages[] = [
        'numeroDocumento' => $escape($numeroDocumento),
        'itemsHtml' => $itemsHtml,
        'exenta' => $exenta,
        'total5' => $total5,
        'total10' => $total10,
        'iva5' => $iva5,
        'iva10' => $iva10,
        'totalIva' => $totalIva,
        'total' => $total,
        'totalLetras' => $this->numero_en_letras($total) .
          ' GUARANÍES',
        'pagina' => $indice + 1,
        'totalPaginas' => $totalPaginas
      ];
    }

    return [
      'escape' => $escape,
      'money' => $money,
      'fechaTexto' => $fechaTexto,
      'horaTexto' => $fecha->format('H:i'),
      'condicionVenta' => $condicionVenta,
      'cliente' => $cliente,
      'ruc' => $ruc,
      'direccion' => $direccion,
      'pages' => $pages
    ];
  }



  /*
   * NÚMEROS EN LETRAS
   */
  private function numero_en_letras(float $valor): string
  {
    $numero = (int) round($valor);

    if ($numero === 0) {
      return 'CERO';
    }

    if ($numero < 0) {
      return 'MENOS ' . $this->numero_en_letras(abs($numero));
    }

    $unidades = [
      '',
      'UNO',
      'DOS',
      'TRES',
      'CUATRO',
      'CINCO',
      'SEIS',
      'SIETE',
      'OCHO',
      'NUEVE',
      'DIEZ',
      'ONCE',
      'DOCE',
      'TRECE',
      'CATORCE',
      'QUINCE',
      'DIECISÉIS',
      'DIECISIETE',
      'DIECIOCHO',
      'DIECINUEVE',
      'VEINTE',
      'VEINTIUNO',
      'VEINTIDÓS',
      'VEINTITRÉS',
      'VEINTICUATRO',
      'VEINTICINCO',
      'VEINTISÉIS',
      'VEINTISIETE',
      'VEINTIOCHO',
      'VEINTINUEVE'
    ];

    $decenas = [
      30 => 'TREINTA',
      40 => 'CUARENTA',
      50 => 'CINCUENTA',
      60 => 'SESENTA',
      70 => 'SETENTA',
      80 => 'OCHENTA',
      90 => 'NOVENTA'
    ];

    $centenas = [
      100 => 'CIENTO',
      200 => 'DOSCIENTOS',
      300 => 'TRESCIENTOS',
      400 => 'CUATROCIENTOS',
      500 => 'QUINIENTOS',
      600 => 'SEISCIENTOS',
      700 => 'SETECIENTOS',
      800 => 'OCHOCIENTOS',
      900 => 'NOVECIENTOS'
    ];

    if ($numero < 30) {
      return $unidades[$numero];
    }

    if ($numero < 100) {
      $decena = intdiv($numero, 10) * 10;
      $resto = $numero % 10;

      return $decenas[$decena] .
        ($resto ? ' Y ' . $unidades[$resto] : '');
    }

    if ($numero < 1000) {
      if ($numero === 100) {
        return 'CIEN';
      }

      $centena = intdiv($numero, 100) * 100;
      $resto = $numero % 100;

      return $centenas[$centena] .
        ($resto ? ' ' . $this->numero_en_letras($resto) : '');
    }

    if ($numero < 1000000) {
      $miles = intdiv($numero, 1000);
      $resto = $numero % 1000;

      $texto = $miles === 1
        ? 'MIL'
        : $this->numero_en_letras($miles) . ' MIL';

      return $texto .
        ($resto ? ' ' . $this->numero_en_letras($resto) : '');
    }

    if ($numero < 1000000000) {
      $millones = intdiv($numero, 1000000);
      $resto = $numero % 1000000;

      $texto = $millones === 1
        ? 'UN MILLÓN'
        : $this->numero_en_letras($millones) . ' MILLONES';

      return $texto .
        ($resto ? ' ' . $this->numero_en_letras($resto) : '');
    }

    $milesMillones = intdiv($numero, 1000000000);
    $resto = $numero % 1000000000;

    $texto = $milesMillones === 1
      ? 'MIL MILLONES'
      : $this->numero_en_letras($milesMillones) . ' MIL MILLONES';

    return $texto .
      ($resto ? ' ' . $this->numero_en_letras($resto) : '');
  }

  /*
   * CONSTRUCCIÓN DEL HTML
   * Genera la factura original y la copia.
   */


  private function print_invoice_two_html(array $data): string
  {
    $e = $data['escape'];
    $m = $data['money'];

    $crearFactura = static function (array $pagina) use ($data, $e, $m): string {
      return '
            <div class="factura">
                <table class="datos">
                    <tr>
                        <td>
                            <strong>Fecha:</strong> ' . $e($data['fechaTexto']) . '
                            &nbsp; <strong>Hora:</strong> ' . $e($data['horaTexto']) . '
                        </td>
                        <td class="condicion">
                            <strong>Condición:</strong> ' . $e($data['condicionVenta']) . '
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Razón Social:</strong> ' . $data['cliente'] . '
                        </td>
                        <td>
                            <strong>RUC:</strong> ' . $data['ruc'] . '
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Dirección:</strong> ' . $data['direccion'] . '
                        </td>
                      <td class="numero-interno">
    <strong>N° Int.:</strong> ' . $e($pagina['numeroDocumento']) . '
</td>
                    </tr>
                </table>

                <table class="productos">
                    <thead>
                        <tr>
                            <th class="codigo">CÓDIGO</th>
                            <th class="cantidad">CANT.</th>
                            <th class="articulo">ARTÍCULOS O SERVICIOS</th>
                            <th class="precio">PRECIO<br>UNITARIO</th>
                            <th class="importe">EXENTA</th>
                            <th class="importe"> 5%</th>
                            <th class="importe"> 10%</th>
                        </tr>
                    </thead>
                    <tbody>
                        ' . $pagina['itemsHtml'] . '
                    </tbody>
                </table>

                <table class="subtotales">
                    <tr>
                        <td colspan="4">
                            <strong>SUBTOTALES</strong>
                        </td>
                        <td class="numero">' . $m($pagina['exenta']) . '</td>
                        <td class="numero">' . $m($pagina['total5']) . '</td>
                        <td class="numero">' . $m($pagina['total10']) . '</td>
                    </tr>
                </table>

                <table class="total">
                    <tr>
                        <td>
                            <strong>TOTAL A PAGAR:</strong>
                            ' . $e($pagina['totalLetras']) . '
                        </td>
                        <td class="importe-total">
                            GS. ' . $m($pagina['total']) . '
                        </td>
                    </tr>
                </table>

                <table class="liquidacion">
                    <tr>
                        <td>
                            <strong>LIQUIDACIÓN DEL IVA:</strong>
                        </td>
                        <td>
                
                        5%: ' . $m($pagina['iva5']) . '
                        </td>
                        <td>
                            10%: ' . $m($pagina['iva10']) . '
                        </td>
                        <td>
                            <strong>TOTAL IVA:</strong>
                            ' . $m($pagina['totalIva']) . '
                        </td>
                    </tr>
                </table>

                <div class="gracias">
                    Agradecemos su preferencia.
                </div>
            </div>';
    };

    $hojasHtml = '';

    foreach ($data['pages'] as $pagina) {
      $original = str_replace(
        'class="factura"',
        'class="factura copia-original"',
        $crearFactura($pagina)
      );

      $duplicado = str_replace(
        'class="factura"',
        'class="factura copia-duplicado"',
        $crearFactura($pagina)
      );

      $hojasHtml .= '
            <div class="hoja">
                ' . $original . '
                ' . $duplicado . '
            </div>';
    }

    return '<!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">

            <style>
                @page {
                    size: A4 portrait;
                    margin: 0;
                }

                html,
                body {
                    margin: 0;
                    padding: 0;
                    width: 210mm;
                }

                body {
                    font-family: Arial, sans-serif;
                }

                .hoja {
                    position: relative;
                    width: 210mm;
                    height: 297mm;
                    margin: 0;
                    padding: 0;
                    overflow: hidden;
                    page-break-after: always;
                }

                .hoja:last-child {
                    page-break-after: auto;
                }

                .factura {
                    position: absolute;
                    left: 15mm;
                    width: 180mm;
                    height: 96mm;
                    box-sizing: border-box;
                    overflow: hidden;
                    margin: 0;
                    padding: 2.5mm;
                    border: 0.3mm solid #000;
                    font-family: Arial, sans-serif;
                    font-size: 9pt;
                    line-height: 1.15;
                }

                .factura.copia-original {
                    top: 45mm;
                }

                .factura.copia-duplicado {
                    top: 185mm;
                }

                .factura table {
                    width: 100%;
                    border-collapse: collapse;
                    table-layout: fixed;
                }

                .factura td,
                .factura th {
                    border: none;
                    padding: 0.5mm 0.8mm;
                    vertical-align: middle;
                    overflow: hidden;
                    overflow-wrap: break-word;
                }

                .datos {
                    height: 15mm;
                    margin-bottom: 1mm;
                    border-bottom: 0.2mm solid #888;
                }

                .datos td {
                    height: 5mm;
                    font-size: 8pt;
                    line-height: 1.1;
                }

                .condicion {
                    width: 35%;
                }

                .numero-interno {
                    text-align: center;
                    font-weight: bold;
                    font-size: 10pt;
                }

                .productos {
                    margin-bottom: 1mm;
                }

                .productos thead {
                    border-top: 0.2mm solid #888;
                    border-bottom: 0.2mm solid #888;
                }

                .productos th {
                    height: 7mm;
                    font-size: 7pt;
                    font-weight: bold;
                    text-align: center;
                    line-height: 1.1;
                }

                .productos td {
                    height: 3.5mm;
                    padding: 0.2mm 0.8mm;
                    font-size: 7.5pt;
                    line-height: 1.05;
                }

                .codigo {
                    width: 12%;
                    text-align: left;
                }

                .cantidad {
                    width: 8%;
                    text-align: center;
                }

                .articulo {
                    width: 30%;
                    text-align: left;
                }

                .precio {
                    width: 13%;
                }

                .importe {
                    width: 12.33%;
                }

                .numero {
                    text-align: right;
                    white-space: nowrap;
                }

                .subtotales {
                    margin-bottom: 1mm;
                    border-top: 0.2mm solid #888;
                    border-bottom: 0.2mm solid #888;
                }

                .subtotales td {
                    height: 5mm;
                    font-size: 8pt;
                }

                .total {
                    margin-bottom: 1mm;
                    border-top: 0.2mm solid #888;
                    border-bottom: 0.2mm solid #888;
                }

                .total td {
                    height: 7mm;
                    font-size: 8pt;
                }

                .importe-total {
                    width: 25%;
                    text-align: right;
                    white-space: nowrap;
                    font-size: 10pt !important;
                    font-weight: bold;
                }

                .liquidacion {
                    margin-bottom: 0.5mm;
                    border: 0.2mm solid #888 !important;
                }

                .liquidacion td {
                    height: 3.5mm;
                    font-size: 7pt;
                    padding: 0.2mm 0.5mm;
                    white-space: nowrap;
                }

                .gracias {
                    border-top: 0.2mm solid #888;
                    padding-top: 0.5mm;
                    margin-top: 0.5mm;
                    font-size: 7.5pt;
                }
            </style>
        </head>

        <body>
            ' . $hojasHtml . '
        </body>
        </html>';
  }



  /*
   * GENERACIÓN DEL PDF
   */
  private function print_invoice_two_pdf(string $html): string
  {
    $options = new Options();
    $options->set('isRemoteEnabled', false);
    $options->set('defaultFont', 'DejaVu Sans');

    $pdf = new Dompdf($options);
    $pdf->loadHtml($html, 'UTF-8');
    $pdf->setPaper('A4', 'portrait');
    $pdf->render();

    return $pdf->output();
  }

  /*
   * RESPUESTA DE ERROR UNIFICADA
   */
  private function error(string $message): array
  {
    return [
      'status' => false,
      'error' => $message
    ];
  }


  /*
   * IMPRIMIR DOCUMENTO CON DATOS YA CARGADOS
   *
   * Reutiliza el diseño original.
   * Se usa tanto para ventas como para facturas guardadas.
   */
  public function print_invoice_two_from_data(
    array $sale,
    array $details,
    bool $requirePrinter = true,
    string $filename = 'factura.pdf'
  ): array {
    try {
      if (!class_exists(Dompdf::class)) {
        return $this->error('DOMPDF NO ESTÁ INSTALADO');
      }

      if (empty($details)) {
        return $this->error('LA FACTURA NO TIENE PRODUCTOS');
      }

      $printer = null;

      if ($requirePrinter) {
        $printerResult = $this->print_invoice_two_printers();

        if (!$printerResult['status']) {
          return $printerResult;
        }

        $printer = $printerResult['printer'];
      }

      // Utilizar exactamente el formateador original.
      $data = $this->print_invoice_two_formatter(
        $sale,
        $details
      );

      // Utilizar exactamente el HTML original.
      $html = $this->print_invoice_two_html($data);

      // Generar el PDF con el mismo diseño.
      $pdf = $this->print_invoice_two_pdf($html);

      // Mantener el comportamiento original de actualización
      // de la secuencia a partir del último documento generado.
      $ultimaPagina = $data['pages'][
        array_key_last($data['pages'])
      ];

      $invoiceSequenceModel =
        new \App\Models\Box\InvoiceSequenceModel();

      $actualizado =
        $invoiceSequenceModel->update_last_number_from_document(
          html_entity_decode(
            $ultimaPagina['numeroDocumento'],
            ENT_QUOTES,
            'UTF-8'
          )
        );

      if (!$actualizado) {
        log_message(
          'error',
          'No se pudo actualizar la secuencia de la factura.'
        );
      }

      return [
        'status' => true,
        'filename' => $filename,
        'pdf' => base64_encode($pdf),
        'printer' => $printer
      ];
    } catch (\Throwable $e) {
      log_message(
        'error',
        'InvoiceService::print_invoice_two_from_data: ' .
        $e->getMessage()
      );

      return $this->error('ERROR AL GENERAR LA FACTURA');
    }
  }




}