<?php

namespace App\Services;

use App\Models\Printers\DevicesPrintersModel;
use App\Models\Customer\CustomersCreditsModel;
use CodeIgniter\Database\BaseConnection;

class TicketService
{
  protected BaseConnection $db;

  protected string $ESC;
  protected string $GS;

  protected array $paymentNames = [
    1 => 'EFECTIVO',
    2 => 'QR',
    3 => 'TRANSFERENCIA',
    4 => 'TARJETA'
  ];

  public function __construct()
  {
    $this->db = \Config\Database::connect();

    $this->ESC = chr(27);
    $this->GS = chr(29);
  }

  /*
   * PREPARAR TICKET PARA QZ TRAY
   */
  public function prepareTicket(
    int $saleId,
    int $documentType,
    int $deviceId
  ): array {
    /*
     * VALIDAR SOLICITUD
     */
    $validation = $this->validateRequest(
      $saleId,
      $documentType,
      $deviceId
    );

    if (!$validation['status']) {
      return $validation;
    }

    /*
     * OBTENER IMPRESORA CONFIGURADA
     */
    $printerResult = $this->getPrinter(
      $deviceId,
      $documentType
    );

    if (!$printerResult['status']) {
      return $printerResult;
    }

    $printer = $printerResult['printer'];

    /*
     * CALCULAR ANCHO DE IMPRESION
     */
    $printableMm = (float) ($printer['printable'] ?? 72);

    if ($printableMm <= 0) {
      $printableMm = 72;
    }

    $lineWidth = max(
      20,
      (int) floor($printableMm / 1.7)
    );

    /*
     * OBTENER VENTA
     */
    $sale = $this->getSale($saleId);

    if (empty($sale)) {
      return [
        'status' => false,
        'error' => 'NO SE ENCONTRO LA VENTA'
      ];
    }

    /*
     * OBTENER CLIENTE
     */
    $customer = $this->getCustomer(
      (int) ($sale['customer'] ?? 0)
    );

    /*
     * OBTENER ARTICULOS
     */
    $details = $this->getSaleDetails($saleId);

    if (empty($details)) {
      return [
        'status' => false,
        'error' => 'LA VENTA NO TIENE ARTICULOS'
      ];
    }

    /*
     * OBTENER PAGOS
     */
    $payments = $this->getPayments($saleId);

    /*
     * OBTENER DEUDA DEL CLIENTE
     */
    $customerDebt = $this->getCustomerDebt($sale);

    /*
     * CONSTRUIR COMANDOS ESC/POS
     */
    $printData = $this->buildTicket(
      $sale,
      $customer,
      $details,
      $payments,
      $customerDebt,
      $documentType,
      $lineWidth,
      (int) ($printer['printer_auto_cut'] ?? 0)
    );

    /*
     * RESPUESTA PARA JAVASCRIPT / QZ TRAY
     */
    return [
      'status' => true,
      'printer' => $printer,
      'print_data' => $printData
    ];
  }

  /*
   * VALIDAR SOLICITUD
   */
  private function validateRequest(
    int $saleId,
    int $documentType,
    int $deviceId
  ): array {
    if ($saleId <= 0) {
      return [
        'status' => false,
        'error' => 'VENTA NO VALIDA'
      ];
    }

    if (!in_array($documentType, [1, 2], true)) {
      return [
        'status' => false,
        'error' => 'TIPO DE DOCUMENTO NO VALIDO'
      ];
    }

    if ($deviceId <= 0) {
      return [
        'status' => false,
        'error' => 'NO SE ENCONTRO EL DISPOSITIVO DE LA SESION'
      ];
    }

    return ['status' => true];
  }

  /*
   * OBTENER IMPRESORA DEL DISPOSITIVO
   */
  private function getPrinter(
    int $deviceId,
    int $documentType
  ): array {
    $printerModel = new DevicesPrintersModel();

    $printer = $printerModel->getPrinterForDevice(
      $deviceId,
      $documentType
    );

    if (empty($printer)) {
      return [
        'status' => false,
        'error' => 'NO HAY UNA IMPRESORA CONFIGURADA PARA ESTE DISPOSITIVO'
      ];
    }

    if (empty($printer['printer_system_name'])) {
      return [
        'status' => false,
        'error' => 'LA IMPRESORA NO TIENE UN NOMBRE DE SISTEMA CONFIGURADO'
      ];
    }

    return [
      'status' => true,
      'printer' => $printer
    ];
  }

  /*
   * OBTENER DATOS DE LA VENTA
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
        's.expedition_point'
      ])
      ->where('s.id', $saleId)
      ->get()
      ->getRowArray();

    return $sale ?: null;
  }

  /*
   * OBTENER CLIENTE
   */
  private function getCustomer(int $customerId): array
  {
    $customer = [
      'name' => 'CONSUMIDOR FINAL',
      'ci' => 'SIN RUC'
    ];

    if ($customerId <= 0) {
      return $customer;
    }

    $row = $this->db->table('customers')
      ->select(['name', 'ci'])
      ->where('id', $customerId)
      ->get()
      ->getRowArray();

    if (!$row) {
      return $customer;
    }

    $name = trim((string) ($row['name'] ?? ''));
    $ci = trim((string) ($row['ci'] ?? ''));

    $customer['name'] = $name !== ''
      ? $name
      : 'CONSUMIDOR FINAL';

    $customer['ci'] = $ci !== ''
      ? $ci
      : 'SIN RUC';

    return $customer;
  }

  /*
   * OBTENER ARTICULOS DE LA VENTA
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
      ->join('products p', 'p.code = sd.product', 'left')
      ->where('sd.sales', $saleId)
      ->get()
      ->getResultArray();
  }

  /*
   * OBTENER PAGOS
   */
  private function getPayments(int $saleId): array
  {
    return $this->db->table('sales_payments')
      ->select([
        'type',
        'amount',
        'number',
        'device'
      ])
      ->where('sales', $saleId)
      ->get()
      ->getResultArray();
  }

  /*
   * OBTENER DEUDA ACTUAL DEL CLIENTE
   */
  private function getCustomerDebt(array $sale): float
  {
    if (
      (int) ($sale['type'] ?? 0) !== 2
      || empty($sale['customer'])
    ) {
      return 0.0;
    }

    $creditsModel = new CustomersCreditsModel();

    $credit = $creditsModel->get_ByCustomer(
      (int) $sale['customer']
    );

    return (float) ($credit['amount'] ?? 0);
  }

  /*
   * CONSTRUIR DOCUMENTO ESC/POS
   */
  private function buildTicket(
    array $sale,
    array $customer,
    array $details,
    array $payments,
    float $customerDebt,
    int $documentType,
    int $lineWidth,
    int $autoCut
  ): array {
    $line = str_repeat('-', $lineWidth);

    $columns = $this->getColumns($lineWidth);
    $totals = $this->calculateTotals($details);
    $paymentInfo = $this->formatPayments($payments);

    $output = [];

    /*
     * ENCABEZADO
     */
    $this->addHeader(
      $output,
      $sale,
      $documentType,
      $line
    );

    /*
     * INFORMACION DE LA VENTA
     */
    $this->addSaleInformation(
      $output,
      $sale,
      $customer,
      $paymentInfo['labels']
    );

    /*
     * ARTICULOS
     */
    $this->addItems(
      $output,
      $details,
      $columns,
      $line
    );

    /*
     * SUBTOTALES, IVA Y TOTAL
     */
    $this->addTotals(
      $output,
      $totals,
      (float) ($sale['total_price'] ?? 0),
      $line
    );

    /*
     * ENTREGA, VUELTO Y COMPROBANTES
     */
    $this->addPaymentDetails(
      $output,
      $sale,
      $paymentInfo
    );

    /*
     * DEUDA DEL CLIENTE
     */
    if ((int) ($sale['type'] ?? 0) === 2) {
      $output[] = $line . "\n";
      $output[] = $this->ESC . 'E' . chr(1);

      $output[] = 'TOTAL DEUDA Gs. '
        . $this->money($customerDebt) . "\n";

      $output[] = $this->ESC . 'E' . chr(0);
    }

    /*
     * PIE DEL DOCUMENTO
     */
    $this->addFooter(
      $output,
      $documentType,
      $line
    );

    /*
     * CORTE AUTOMATICO
     */
    if ($autoCut === 1) {
      $output[] = "\n\n\n\n\n\n";
      $output[] = $this->GS . 'V' . chr(0);
    }

    return $output;
  }

  /*
   * CALCULAR ANCHO DE LAS COLUMNAS
   */

  /*
   * CALCULAR ANCHO DE LAS COLUMNAS
   */
  private function getColumns(int $lineWidth): array
  {
    $quantity = 6;
    $price = 10;
    $total = 10;

    $description = $lineWidth
      - $quantity
      - $price
      - $total;

    /*
     * Si la impresora tiene un ancho reducido,
     * reducir primero las columnas de importes.
     */
    if ($description < 5) {
      $quantity = 5;
      $price = 8;
      $total = 8;

      $description = $lineWidth
        - $quantity
        - $price
        - $total;
    }

    return [
      'description' => max(1, $description),
      'quantity' => $quantity,
      'price' => $price,
      'total' => $total
    ];
  }


  /*
   * ENCABEZADO DEL DOCUMENTO
   */
  private function addHeader(
    array &$output,
    array $sale,
    int $documentType,
    string $line
  ): void {
    $output[] = $this->ESC . '@';
    $output[] = $this->ESC . 'a' . chr(1);
    $output[] = $this->ESC . 'E' . chr(1);
    $output[] = "FILI\n";
    $output[] = $this->ESC . 'E' . chr(0);
    $output[] = "CENTRO DE COMPRAS\n";

    $output[] = $this->ESC . 'a' . chr(0);
    $output[] = $line . "\n";

    $label = $documentType === 1
      ? 'TICKET NRO: '
      : 'FACTURA NRO: ';

    $output[] = $label
      . ($sale['number'] ?? $sale['id'])
      . "\n";

    $output[] = $line . "\n";
  }

  /*
   * DATOS GENERALES DE LA VENTA
   */
  private function addSaleInformation(
    array &$output,
    array $sale,
    array $customer,
    array $paymentLabels
  ): void {
    $condition = match ((int) ($sale['type'] ?? 1)) {
      2 => 'CREDITO',
      3 => 'DEVOLUCION',
      4 => 'ESPERA',
      default => 'CONTADO'
    };

    $output[] = 'CONDICION: ' . $condition . "\n";

    $output[] = 'FECHA: '
      . ($sale['date'] ?? '')
      . ' '
      . ($sale['time'] ?? '')
      . "\n";

    $output[] = 'CLIENTE: '
      . $customer['name'] . "\n";

    $output[] = 'RUC: '
      . $customer['ci'] . "\n";

    $output[] = 'PAGO: '
      . implode('/', $paymentLabels) . "\n";
  }


  /*
   * IMPRIMIR ARTICULOS
   */
  private function addItems(
    array &$output,
    array $details,
    array $columns,
    string $line
  ): void {
    $output[] = $line . "\n";

    $output[] =
      $this->fitText('ARTICULO', $columns['description'])
      . $this->pad('CANT', $columns['quantity'], true)
      . $this->pad('PRECIO', $columns['price'], true)
      . $this->pad('MONTO', $columns['total'], true)
      . "\n";

    $output[] = $line . "\n";

    foreach ($details as $item) {
      $output[] = $this->formatItem(
        $item,
        $columns
      );
    }

    $output[] = $line . "\n";
  }


  /*
   * FORMATEAR UNA LINEA DE ARTICULO
   */
  private function formatItem(
    array $item,
    array $columns
  ): string {
    $description = (string) (
      $item['product_description']
      ?? $item['product']
      ?? 'ARTICULO'
    );

    $quantity = (float) ($item['cant'] ?? 0);
    $unitPrice = (float) ($item['unit_price'] ?? 0);
    $totalPrice = (float) ($item['total_price'] ?? 0);

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

    return
      $this->fitText(
        $description,
        $columns['description']
      )
      . $this->pad(
        $this->fitText(
          $quantityText,
          $columns['quantity']
        ),
        $columns['quantity'],
        true
      )
      . $this->pad(
        $this->fitText(
          $this->money($unitPrice),
          $columns['price']
        ),
        $columns['price'],
        true
      )
      . $this->pad(
        $this->fitText(
          $this->money($totalPrice),
          $columns['total']
        ),
        $columns['total'],
        true
      )
      . "\n";
  }


  /*
   * CALCULAR SUBTOTALES E IVA
   * SE CONSIDERA QUE LOS PRECIOS YA INCLUYEN IVA.
   */
  private function calculateTotals(array $details): array
  {
    $subtotal5 = 0.0;
    $subtotal10 = 0.0;
    $subtotalExempt = 0.0;

    foreach ($details as $detail) {
      $amount = (float) ($detail['total_price'] ?? 0);

      switch ((int) ($detail['type_iva'] ?? 3)) {
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

  /*
   * IMPRIMIR SUBTOTALES, IVA Y TOTAL
   */
  private function addTotals(
    array &$output,
    array $totals,
    float $total,
    string $line
  ): void {
    if ($totals['subtotal5'] > 0) {
      $output[] = 'Subtotal 5% Gs. '
        . $this->money($totals['subtotal5']) . "\n";

      $output[] = 'IVA 5% Gs. '
        . $this->money($totals['iva5']) . "\n";
    }

    if ($totals['subtotal10'] > 0) {
      $output[] = 'Subtotal 10% Gs. '
        . $this->money($totals['subtotal10']) . "\n";

      $output[] = 'IVA 10% Gs. '
        . $this->money($totals['iva10']) . "\n";
    }

    if ($totals['subtotal_exempt'] > 0) {
      $output[] = 'Subtotal exenta Gs. '
        . $this->money($totals['subtotal_exempt']) . "\n";
    }

    $output[] = $line . "\n";
    $output[] = $this->ESC . 'E' . chr(1);
    $output[] = 'TOTAL Gs. '
      . $this->money($total) . "\n";
    $output[] = $this->ESC . 'E' . chr(0);
  }

  /*
   * PREPARAR LOS MEDIOS DE PAGO
   */
  private function formatPayments(array $payments): array
  {
    $labels = [];
    $isCash = false;
    $receipts = [];

    foreach ($payments as $payment) {
      $type = (int) ($payment['type'] ?? 0);

      $name = $this->paymentNames[$type] ?? 'OTRO';

      $labels[] = $name;

      if ($type === 1) {
        $isCash = true;
      }

      if (
        $type !== 1
        && !empty($payment['number'])
      ) {
        $receipts[] = [
          'name' => $name,
          'number' => (string) $payment['number']
        ];
      }
    }

    $labels = array_values(array_unique($labels));

    if (empty($labels)) {
      $labels[] = 'NO REGISTRADO';
    }

    return [
      'labels' => $labels,
      'is_cash' => $isCash,
      'receipts' => $receipts
    ];
  }

  /*
   * IMPRIMIR ENTREGA, VUELTO Y COMPROBANTES
   */
  private function addPaymentDetails(
    array &$output,
    array $sale,
    array $paymentInfo
  ): void {
    if ($paymentInfo['is_cash']) {
      $cashReceived = (float) ($sale['cash_received'] ?? 0);
      $cashChange = (float) ($sale['cash_change'] ?? 0);

      if ($cashReceived > 0) {
        $output[] = 'ENTREGA Gs. '
          . $this->money($cashReceived) . "\n";

        $output[] = 'VUELTO Gs. '
          . $this->money($cashChange) . "\n";
      }
    }

    foreach ($paymentInfo['receipts'] as $receipt) {
      $output[] = $receipt['name']
        . ' NRO: '
        . $receipt['number']
        . "\n";
    }
  }

  /*
   * PIE DEL DOCUMENTO
   */
  private function addFooter(
    array &$output,
    int $documentType,
    string $line
  ): void {
    $output[] = $line . "\n";
    $output[] = $this->ESC . 'a' . chr(1);
    $output[] = "Muchas gracias por su compra\n";

    if ($documentType === 1) {
      $output[] = "No valido como factura\n";
    }

    $output[] = "\n\n\n";
  }

  /*
   * FORMATEAR GUARANIES
   */
  private function money(float $amount): string
  {
    return number_format($amount, 0, ',', '.');
  }

  /*
   * AJUSTAR TEXTO A UN ANCHO
   */
  private function fitText(
    string $text,
    int $width
  ): string {
    $text = trim($text);
    $width = max(1, $width);

    if (
      function_exists('mb_strwidth')
      && function_exists('mb_strimwidth')
    ) {
      if (mb_strwidth($text, 'UTF-8') > $width) {
        $text = mb_strimwidth(
          $text,
          0,
          $width,
          '',
          'UTF-8'
        );
      }

      $length = mb_strwidth($text, 'UTF-8');
    } else {
      $characters = preg_split(
        '//u',
        $text,
        -1,
        PREG_SPLIT_NO_EMPTY
      );

      if ($characters !== false) {
        $text = implode(
          '',
          array_slice($characters, 0, $width)
        );
      } else {
        $text = substr($text, 0, $width);
      }

      $length = strlen($text);
    }

    return $text . str_repeat(
      ' ',
      max(0, $width - $length)
    );
  }

  /*
   * ALINEAR TEXTO EN UNA COLUMNA
   */
  private function pad(
    string $text,
    int $width,
    bool $right = false
  ): string {
    $text = trim($text);
    $width = max(1, $width);

    if (
      function_exists('mb_strwidth')
      && function_exists('mb_strimwidth')
    ) {
      $length = mb_strwidth($text, 'UTF-8');

      if ($length > $width) {
        $text = mb_strimwidth(
          $text,
          0,
          $width,
          '',
          'UTF-8'
        );

        $length = mb_strwidth($text, 'UTF-8');
      }
    } else {
      $length = strlen($text);

      if ($length > $width) {
        $text = substr($text, 0, $width);
        $length = strlen($text);
      }
    }

    $spaces = str_repeat(
      ' ',
      max(0, $width - $length)
    );

    return $right
      ? $spaces . $text
      : $text . $spaces;
  }
}
