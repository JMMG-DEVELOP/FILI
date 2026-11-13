<?php

namespace App\Services;

use App\Models\Printers\DevicesPrintersModel;
use Dompdf\Dompdf;
use Dompdf\Options;
use CodeIgniter\Database\BaseConnection;

class TicketPdfService
{
  private BaseConnection $db;

  public function __construct()
  {
    $this->db = \Config\Database::connect();
  }

  /**
   * Genera el PDF de respaldo de una venta.
   *
   * documentType:
   * 1 = Ticket
   * 2 = Factura
   */
  public function generatePdf(
    int $saleId,
    int $documentType,
    int $deviceId
  ): array {
    if (
      $saleId <= 0
      || !in_array($documentType, [1, 2], true)
    ) {
      throw new \InvalidArgumentException(
        'DATOS DE IMPRESION NO VALIDOS'
      );
    }

    if (!class_exists(Dompdf::class)) {
      throw new \RuntimeException(
        'DOMPDF NO ESTA INSTALADO'
      );
    }

    $printableMm = $this->getPrintableWidth(
      $deviceId,
      $documentType
    );

    $sale = $this->getSale($saleId);

    if (!$sale) {
      throw new \RuntimeException(
        'NO SE ENCONTRO LA VENTA'
      );
    }

    $details = $this->getSaleDetails($saleId);

    if (empty($details)) {
      throw new \RuntimeException(
        'LA VENTA NO TIENE ARTICULOS'
      );
    }

    $payments = $this->getPayments($saleId);

    $totals = $this->calculateTotals($details);

    $html = $this->buildHtml(
      $sale,
      $details,
      $payments,
      $totals,
      $documentType
    );

    $pdf = $this->renderPdf(
      $html,
      $printableMm,
      count($details),
      count($payments)
    );

    return [
      'status' => true,
      'filename' => 'venta_' . $saleId . '.pdf',
      'pdf' => base64_encode($pdf),
      'printable_mm' => $printableMm
    ];
  }

  /**
   * Obtiene el ancho configurado para la impresora.
   */
  private function getPrintableWidth(
    int $deviceId,
    int $documentType
  ): float {
    $printableMm = 72.00;

    if ($deviceId <= 0) {
      return $printableMm;
    }

    $printerModel = new DevicesPrintersModel();

    $printer = $printerModel->getPrinterForDevice(
      $deviceId,
      $documentType
    );

    if (!empty($printer['printable'])) {
      $configuredWidth = (float) $printer['printable'];

      if ($configuredWidth > 0) {
        $printableMm = $configuredWidth;
      }
    }

    return $printableMm;
  }

  /**
   * Datos principales de la venta.
   */
  private function getSale(int $saleId): ?array
  {
    $sale = $this->db->table('sales s')
      ->select([
        's.id',
        's.number',
        's.date',
        's.time',
        's.type',
        's.total_price',
        's.cash_received',
        's.cash_change',
        's.customer',
        's.user',
        's.sucursal',
        's.expedition_point',
        's.invoice_type',
        'c.name AS customer_name',
        'c.ci AS customer_ci',
        'u.name AS user_name',
        'su.name AS sucursal_name',
        'ep.code AS expedition_point_code'
      ])
      ->join(
        'customers c',
        'c.id = s.customer',
        'left'
      )
      ->join(
        'users u',
        'u.id = s.user',
        'left'
      )
      ->join(
        'sucursals su',
        'su.id = s.sucursal',
        'left'
      )
      ->join(
        'expedition_point ep',
        'ep.id = s.expedition_point',
        'left'
      )
      ->where('s.id', $saleId)
      ->get()
      ->getRowArray();

    return $sale ?: null;
  }

  /**
   * Artículos de la venta.
   */
  private function getSaleDetails(int $saleId): array
  {
    return $this->db->table('sales_details sd')
      ->select([
        'sd.product',
        'sd.cant',
        'sd.unit_price',
        'sd.total_price',
        'sd.type_iva',
        'p.description AS product_description'
      ])
      ->join(
        'products p',
        'p.code = sd.product',
        'left'
      )
      ->where('sd.sales', $saleId)
      ->get()
      ->getResultArray();
  }

  /**
   * Formas de pago registradas.
   */
  private function getPayments(int $saleId): array
  {
    return $this->db->table('sales_payments sp')
      ->select([
        'sp.type',
        'sp.amount',
        'sp.number',
        'sp.device',
        'pt.name AS payment_name'
      ])
      ->join(
        'payment_type pt',
        'pt.id = sp.type',
        'left'
      )
      ->where('sp.sales', $saleId)
      ->get()
      ->getResultArray();
  }

  /**
   * Calcula subtotales e IVA incluido en los precios.
   *
   * sales_iva:
   * 1 = 10%
   * 2 = 5%
   * 3 = Exenta
   */
  private function calculateTotals(array $details): array
  {
    $subtotal5 = 0.0;
    $subtotal10 = 0.0;
    $subtotalExempt = 0.0;

    foreach ($details as $item) {
      $amount = (float) ($item['total_price'] ?? 0);

      switch ((int) ($item['type_iva'] ?? 3)) {
        case 1:
          $subtotal10 += $amount;
          break;

        case 2:
          $subtotal5 += $amount;
          break;

        default:
          $subtotalExempt += $amount;
          break;
      }
    }

    return [
      'subtotal5' => $subtotal5,
      'subtotal10' => $subtotal10,
      'subtotal_exempt' => $subtotalExempt,
      'iva5' => round($subtotal5 / 21),
      'iva10' => round($subtotal10 / 11)
    ];
  }

  /**
   * Construye el HTML del comprobante.
   */
  private function buildHtml(
    array $sale,
    array $details,
    array $payments,
    array $totals,
    int $documentType
  ): string {
    $escape = static function ($value): string {
      return htmlspecialchars(
        (string) ($value ?? ''),
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
      );
    };

    $money = static function ($amount): string {
      return number_format(
        (float) $amount,
        0,
        ',',
        '.'
      );
    };

    $customerName = trim(
      (string) ($sale['customer_name'] ?? '')
    );

    $customerCi = trim(
      (string) ($sale['customer_ci'] ?? '')
    );

    if ($customerName === '') {
      $customerName = 'CONSUMIDOR FINAL';
    }

    if ($customerCi === '') {
      $customerCi = 'SIN RUC';
    }

    $condition = match ((int) ($sale['type'] ?? 1)) {
      2 => 'CREDITO',
      3 => 'DEVOLUCION',
      4 => 'ESPERA',
      default => 'CONTADO'
    };

    $documentTitle = $documentType === 1
      ? 'TICKET'
      : 'FACTURA';

    /*
     * ARTICULOS
     *
     * Se elimina la columna IVA para aprovechar
     * ese espacio en la descripción.
     */
    $itemsHtml = '';

    foreach ($details as $item) {
      $description = trim(
        (string) (
          $item['product_description']
          ?: $item['product']
          ?: 'ARTICULO'
        )
      );

      $quantity = (float) ($item['cant'] ?? 0);

      $quantityText = rtrim(
        rtrim(
          number_format($quantity, 3, ',', '.'),
          '0'
        ),
        ','
      );

      if ($quantityText === '') {
        $quantityText = '0';
      }

      $itemsHtml .= '
        <tr>
          <td class="description">'
        . $escape($description) .
        '</td>
          <td class="quantity center">'
        . $escape($quantityText) .
        '</td>
          <td class="price right">'
        . $escape($money($item['unit_price'] ?? 0)) .
        '</td>
          <td class="amount right">'
        . $escape($money($item['total_price'] ?? 0)) .
        '</td>
        </tr>';
    }

    /*
     * DESGLOSE DEL IVA
     */
    $ivaHtml = '';

    if ($totals['subtotal5'] > 0) {
      $ivaHtml .= '
        <tr>
          <td>Subtotal 5%</td>
          <td class="right">Gs. '
        . $money($totals['subtotal5']) .
        '</td>
        </tr>
        <tr>
          <td>IVA 5%</td>
          <td class="right">Gs. '
        . $money($totals['iva5']) .
        '</td>
        </tr>';
    }

    if ($totals['subtotal10'] > 0) {
      $ivaHtml .= '
        <tr>
          <td>Subtotal 10%</td>
          <td class="right">Gs. '
        . $money($totals['subtotal10']) .
        '</td>
        </tr>
        <tr>
          <td>IVA 10%</td>
          <td class="right">Gs. '
        . $money($totals['iva10']) .
        '</td>
        </tr>';
    }

    if ($totals['subtotal_exempt'] > 0) {
      $ivaHtml .= '
        <tr>
          <td>Subtotal exenta</td>
          <td class="right">Gs. '
        . $money($totals['subtotal_exempt']) .
        '</td>
        </tr>';
    }

    /*
     * PAGOS
     */
    $paymentsHtml = '';
    $paymentLabels = [];
    $paymentReceipts = [];
    $isCashPayment = false;

    $paymentNames = [
      1 => 'EFECTIVO',
      2 => 'QR',
      3 => 'TRANSFERENCIA',
      4 => 'TARJETA'
    ];

    foreach ($payments as $payment) {
      $paymentType = (int) ($payment['type'] ?? 0);

      $paymentName = trim(
        (string) ($payment['payment_name'] ?? '')
      );

      if ($paymentName === '') {
        $paymentName = $paymentNames[$paymentType] ?? 'OTRO';
      }

      $paymentLabels[] = $paymentName;

      if ($paymentType === 1) {
        $isCashPayment = true;
      }

      $paymentsHtml .= '
        <tr>
          <td>' . $escape($paymentName) . '</td>
          <td class="right">Gs. '
        . $money($payment['amount'] ?? 0) .
        '</td>
        </tr>';

      if (
        $paymentType !== 1
        && !empty($payment['number'])
      ) {
        $paymentReceipts[] = [
          'name' => $paymentName,
          'number' => (string) $payment['number']
        ];

        $paymentsHtml .= '
          <tr>
            <td colspan="2" class="small">
              Comprobante Nro.: '
          . $escape($payment['number']) .
          '</td>
          </tr>';
      }
    }

    $paymentLabels = array_values(
      array_unique($paymentLabels)
    );

    if (empty($paymentLabels)) {
      $paymentLabels[] = 'NO REGISTRADO';
    }

    /*
     * ENTREGA Y VUELTO
     */
    $cashHtml = '';

    if (
      $isCashPayment
      && (float) ($sale['cash_received'] ?? 0) > 0
    ) {
      $cashHtml = '
        <table class="summary">
          <tr>
            <td>ENTREGA</td>
            <td class="right">Gs. '
        . $money($sale['cash_received']) .
        '</td>
          </tr>
          <tr>
            <td>VUELTO</td>
            <td class="right">Gs. '
        . $money($sale['cash_change'] ?? 0) .
        '</td>
          </tr>
        </table>';
    }

    /*
     * DEUDA ACTUAL DEL CLIENTE
     */
    $creditHtml = '';

    if (
      (int) ($sale['type'] ?? 0) === 2
      && !empty($sale['customer'])
    ) {
      $credit = $this->db->table('customers_credits')
        ->select('amount')
        ->where('customer', $sale['customer'])
        ->get()
        ->getRowArray();

      if ($credit) {
        $creditHtml = '
          <div class="separator"></div>
          <table class="summary">
            <tr>
              <td><strong>DEUDA ACTUAL</strong></td>
              <td class="right"><strong>Gs. '
          . $money($credit['amount'] ?? 0) .
          '</strong></td>
            </tr>
          </table>';
      }
    }

    /*
     * HTML DEL TICKET
     */
    return '
      <!DOCTYPE html>
      <html lang="es">
      <head>
        <meta charset="UTF-8">

        <style>
          @page {
            margin: 2mm;
          }

          * {
            box-sizing: border-box;
          }

          body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 8px;
            color: #000;
            margin: 0;
            padding: 0;
          }

          h1, p {
            margin: 0;
            padding: 0;
          }

          .header {
            text-align: center;
            margin-bottom: 5px;
          }

          .brand {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 2px;
          }

          .subtitle {
            font-size: 9px;
            margin-bottom: 3px;
          }

          .document-title {
            font-size: 11px;
            font-weight: bold;
            margin-top: 4px;
          }

          .separator {
            border-top: 1px dashed #000;
            margin: 5px 0;
          }

          .info,
          .summary {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
          }

          .info td,
          .summary td {
            border: 0;
            padding: 2px 0;
            vertical-align: top;
            overflow-wrap: anywhere;
          }

          .info .label {
            width: 36%;
            font-weight: bold;
          }

          table.products {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            margin-top: 5px;
            font-size: 7px;
          }

          .products th,
          .products td {
            padding: 3px 1px;
            border-bottom: 1px dashed #aaa;
            vertical-align: top;
            overflow-wrap: anywhere;
          }

          .products th {
            font-weight: bold;
            border-bottom: 1px solid #000;
          }

          .description {
            width: 48%;
            text-align: left;
          }

          .quantity {
            width: 13%;
          }

          .price {
            width: 19%;
          }

          .amount {
            width: 20%;
          }

          .center {
            text-align: center;
          }

          .right {
            text-align: right;
          }

          .total {
            font-size: 11px;
            font-weight: bold;
            border-top: 1px solid #000 !important;
            padding-top: 5px !important;
          }

          .footer {
            text-align: center;
            margin-top: 10px;
            font-size: 8px;
          }

          .footer p {
            margin-top: 3px;
          }

          .small {
            font-size: 7px;
          }
        </style>
      </head>

      <body>

        <div class="header">
          <h1 class="brand">FILI</h1>
          <p class="subtitle">CENTRO DE COMPRAS</p>
          <p class="document-title">'
      . $escape($documentTitle) .
      '</p>
        </div>

        <div class="separator"></div>

        <table class="info">
          <tr>
            <td class="label">Venta Nro.:</td>
            <td>'
      . $escape($sale['number'] ?? $sale['id']) .
      '</td>
          </tr>
          <tr>
            <td class="label">Condición:</td>
            <td>' . $escape($condition) . '</td>
          </tr>
          <tr>
            <td class="label">Fecha:</td>
            <td>'
      . $escape($sale['date'] ?? '') . ' '
      . $escape($sale['time'] ?? '') .
      '</td>
          </tr>
          <tr>
            <td class="label">Sucursal:</td>
            <td>'
      . $escape($sale['sucursal_name'] ?? '') .
      '</td>
          </tr>
          <tr>
            <td class="label">Punto expedición:</td>
            <td>'
      . $escape($sale['expedition_point_code'] ?? '') .
      '</td>
          </tr>
          <tr>
            <td class="label">Cliente:</td>
            <td>' . $escape($customerName) . '</td>
          </tr>
          <tr>
            <td class="label">RUC/CI:</td>
            <td>' . $escape($customerCi) . '</td>
          </tr>
          <tr>
            <td class="label">Usuario:</td>
            <td>' . $escape($sale['user_name'] ?? '') . '</td>
          </tr>
          <tr>
            <td class="label">Pago:</td>
            <td>'
      . $escape(implode(' / ', $paymentLabels)) .
      '</td>
          </tr>
        </table>

        <div class="separator"></div>

        <table class="products">
          <thead>
            <tr>
              <th class="description">ARTICULO</th>
              <th class="quantity center">CANT.</th>
              <th class="price right">PRECIO</th>
              <th class="amount right">MONTO</th>
            </tr>
          </thead>
          <tbody>'
      . $itemsHtml .
      '</tbody>
        </table>

        <div class="separator"></div>

        <table class="summary">
          <tbody>'
      . $ivaHtml .
      '<tr>
              <td class="total">TOTAL Gs.</td>
              <td class="right total">'
      . $money($sale['total_price'] ?? 0) .
      '</td>
            </tr>
          </tbody>
        </table>

        ' . $cashHtml . '

        <div class="separator"></div>

        <strong>DETALLE DE PAGOS</strong>

        <table class="summary">
          <tbody>'
      . $paymentsHtml .
      '</tbody>
        </table>

        ' . $creditHtml . '

        <div class="footer">
          <p>Muchas gracias por su compra</p>'
      . ($documentType === 1
        ? '<p>No válido como factura</p>'
        : '') .
      '
        </div>

      </body>
      </html>';
  }

  /**
   * Renderiza el PDF térmico.
   */
  private function renderPdf(
    string $html,
    float $printableMm,
    int $detailCount,
    int $paymentCount
  ): string {
    $options = new Options();

    $options->set('isRemoteEnabled', false);
    $options->set('defaultFont', 'DejaVu Sans');
    $options->set('isHtml5ParserEnabled', true);

    $pdf = new Dompdf($options);

    $pdf->loadHtml($html, 'UTF-8');

    // Márgenes laterales de 2 mm a cada lado.
    $pageWidthMm = $printableMm + 4;

    // Altura estimada para evitar cortar los artículos.
    $heightMm = max(
      100,
      90 + ($detailCount * 9) + ($paymentCount * 8)
    );

    $mmToPoints = 2.83464567;

    $pdf->setPaper([
      0,
      0,
      $pageWidthMm * $mmToPoints,
      $heightMm * $mmToPoints
    ]);

    $pdf->render();

    return $pdf->output();
  }
}

