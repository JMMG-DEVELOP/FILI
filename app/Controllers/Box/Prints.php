<?php
namespace App\Controllers\Box;

use App\Controllers\BaseController;
use App\Models\Printers\DevicesPrintersModel;
use App\Models\Customer\CustomersCreditsModel;
class Prints extends BaseController
{


  public function print_data()
  {
    try {
      $saleId = (int) $this->request->getPost('sale_id');
      $documentType = (int) $this->request->getPost('type');
      $deviceId = (int) session('device');

      if ($saleId <= 0) {
        return $this->response->setJSON([
          'status' => false,
          'error' => 'VENTA NO VALIDA'
        ]);
      }

      if (!in_array($documentType, [1, 2], true)) {
        return $this->response->setJSON([
          'status' => false,
          'error' => 'TIPO DE DOCUMENTO NO VALIDO'
        ]);
      }

      if ($deviceId <= 0) {
        return $this->response->setJSON([
          'status' => false,
          'error' => 'NO SE ENCONTRO EL DISPOSITIVO DE LA SESION'
        ]);
      }

      $db = \Config\Database::connect();

      /*
       * CONFIGURACION DE IMPRESORA
       */
      $printerModel = new DevicesPrintersModel();

      $printer = $printerModel->getPrinterForDevice(
        $deviceId,
        $documentType
      );

      if (empty($printer)) {
        return $this->response->setJSON([
          'status' => false,
          'error' => 'NO HAY UNA IMPRESORA CONFIGURADA PARA ESTE DISPOSITIVO'
        ]);
      }

      if (empty($printer['printer_system_name'])) {
        return $this->response->setJSON([
          'status' => false,
          'error' => 'LA IMPRESORA NO TIENE UN NOMBRE DE SISTEMA CONFIGURADO'
        ]);
      }

      /*
       * ANCHO IMPRIMIBLE
       * El campo printable contiene milímetros, no caracteres.
       */
      $printableMm = (float) ($printer['printable'] ?? 72);

      if ($printableMm <= 0) {
        $printableMm = 72;
      }

      /*
       * Aproximación para fuente monoespaciada ESC/POS.
       * 72 mm suelen admitir aproximadamente 42 caracteres.
       */
      $lineWidth = max(20, (int) floor($printableMm / 1.7));

      /*
       * DATOS DE LA VENTA
       */
      $sale = $db->table('sales s')
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

      if (empty($sale)) {
        return $this->response->setJSON([
          'status' => false,
          'error' => 'NO SE ENCONTRO LA VENTA'
        ]);
      }
      /*
       * DEUDA TOTAL DEL CLIENTE
       * Consultar solamente para ventas a crédito.
       */
      $customerDebt = 0.0;

      if ((int) $sale['type'] === 2 && !empty($sale['customer'])) {
        $creditsModel = new CustomersCreditsModel();

        $credit = $creditsModel->get_ByCustomer(
          (int) $sale['customer']
        );

        $customerDebt = (float) ($credit['amount'] ?? 0);
      }

      /*
       * CLIENTE
       */
      $customerName = 'CONSUMIDOR FINAL';
      $customerCi = 'SIN RUC';

      if (!empty($sale['customer'])) {
        $customer = $db->table('customers')
          ->select(['name', 'ci'])
          ->where('id', $sale['customer'])
          ->get()
          ->getRowArray();

        if ($customer) {
          $customerName = trim((string) ($customer['name'] ?? 'CONSUMIDOR FINAL'));
          $customerCi = trim((string) ($customer['ci'] ?? 'SIN RUC'));

          if ($customerName === '') {
            $customerName = 'CONSUMIDOR FINAL';
          }

          if ($customerCi === '') {
            $customerCi = 'SIN RUC';
          }
        }
      }

      /*
       * DETALLES DE LA VENTA
       */
      $details = $db->table('sales_details sd')
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

      if (empty($details)) {
        return $this->response->setJSON([
          'status' => false,
          'error' => 'LA VENTA NO TIENE ARTICULOS'
        ]);
      }

      /*
       * PAGOS
       */
      $payments = $db->table('sales_payments')
        ->select(['type', 'amount', 'number', 'device'])
        ->where('sales', $saleId)
        ->get()
        ->getResultArray();

      $paymentNames = [
        1 => 'EFECTIVO',
        2 => 'QR',
        3 => 'TRANSFERENCIA',
        4 => 'TARJETA'
      ];

      /*
       * FORMATO DE GUARANIES
       */
      $money = static function ($amount): string {
        return number_format((float) $amount, 0, ',', '.');
      };

      /*
       * AJUSTAR TEXTO A UNA COLUMNA
       */
      $fitText = static function ($text, int $width): string {
        $text = trim((string) $text);
        $width = max(1, $width);

        if (function_exists('mb_strwidth') && function_exists('mb_strimwidth')) {
          // Recortar solo si el texto supera el ancho disponible.
          if (mb_strwidth($text, 'UTF-8') > $width) {
            $text = mb_strimwidth(
              $text,
              0,
              $width,
              '',
              'UTF-8'
            );
          }

          $textLength = mb_strwidth($text, 'UTF-8');
        } else {
          // Evitar cortar un carácter UTF-8 a la mitad.
          if (function_exists('iconv')) {
            $characters = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);

            if ($characters !== false) {
              $text = implode('', array_slice($characters, 0, $width));
            } else {
              $text = substr($text, 0, $width);
            }
          } else {
            $text = substr($text, 0, $width);
          }

          $textLength = strlen($text);
        }

        return $text . str_repeat(' ', max(0, $width - $textLength));
      };

      /*
       * LINEAS Y COLUMNAS PROPORCIONALES
       */
      $line = str_repeat('-', $lineWidth);

      $descriptionWidth = max(5, (int) floor($lineWidth * 0.29));
      $quantityWidth = max(3, (int) floor($lineWidth * 0.12));
      $ivaWidth = max(3, (int) floor($lineWidth * 0.10));
      $priceWidth = max(5, (int) floor($lineWidth * 0.21));
      $totalWidth = $lineWidth
        - $descriptionWidth
        - $quantityWidth
        - $ivaWidth
        - $priceWidth;

      // Asegurar que la suma de columnas sea exactamente el ancho disponible.
      if ($totalWidth < 5) {
        $descriptionWidth -= 5 - $totalWidth;
        $totalWidth = 5;
      }

      $descriptionWidth = max(1, $descriptionWidth);
      $totalWidth = $lineWidth
        - $descriptionWidth
        - $quantityWidth
        - $ivaWidth
        - $priceWidth;
      /*
       * Si el ancho es reducido, conservar al menos una columna
       * de importe y ajustar la descripción.
       */
      if ($totalWidth < 5) {
        $descriptionWidth = max(
          4,
          $descriptionWidth - (5 - $totalWidth)
        );

        $totalWidth = $lineWidth
          - $descriptionWidth
          - $quantityWidth
          - $ivaWidth
          - $priceWidth;
      }

      $totalWidth = max(5, $totalWidth);

      /*
       * CONSTRUIR UNA LINEA POR ARTICULO
       */
      $formatItem = static function (array $item) use ($fitText, $descriptionWidth, $quantityWidth, $ivaWidth, $priceWidth, $totalWidth, $money): string {
        $description = $item['product_description']
          ?? $item['product']
          ?? 'ARTICULO';

        $quantity = (float) ($item['cant'] ?? 0);
        $unitPrice = (float) ($item['unit_price'] ?? 0);
        $totalPrice = (float) ($item['total_price'] ?? 0);

        $ivaType = (int) ($item['type_iva'] ?? 3);

        $ivaLabel = match ($ivaType) {
          1 => '10%',
          2 => '5%',
          default => 'EXE'
        };

        $quantityText = rtrim(
          rtrim(number_format($quantity, 3, ',', '.'), '0'),
          ','
        );

        if ($quantityText === '') {
          $quantityText = '0';
        }

        $priceText = $money($unitPrice);
        $totalText = $money($totalPrice);

        /*
         * Recortar importes si no caben en el ancho disponible.
         */
        $priceText = substr($priceText, 0, $priceWidth);
        $totalText = substr($totalText, 0, $totalWidth);

        return
          $fitText($description, $descriptionWidth)
          . str_pad(
            substr($quantityText, 0, $quantityWidth),
            $quantityWidth,
            ' ',
            STR_PAD_LEFT
          )
          . str_pad(
            $ivaLabel,
            $ivaWidth,
            ' ',
            STR_PAD_LEFT
          )
          . str_pad(
            $priceText,
            $priceWidth,
            ' ',
            STR_PAD_LEFT
          )
          . str_pad(
            $totalText,
            $totalWidth,
            ' ',
            STR_PAD_LEFT
          )
          . "\n";
      };

      /*
       * CALCULAR SUBTOTALES POR TIPO DE IVA
       */
      $subtotal5 = 0;
      $subtotal10 = 0;
      $subtotalExempt = 0;

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

      /*
       * Se considera que los precios ya incluyen IVA.
       */
      $iva5 = round($subtotal5 / 21);
      $iva10 = round($subtotal10 / 11);

      $total = (float) ($sale['total_price'] ?? 0);

      /*
       * PREPARAR TEXTO DE PAGOS
       */
      $paymentLabels = [];
      $isCashPayment = false;
      $paymentReceipts = [];

      foreach ($payments as $payment) {
        $paymentType = (int) ($payment['type'] ?? 0);

        $paymentName = $paymentNames[$paymentType] ?? 'OTRO';

        $paymentLabels[] = $paymentName;
        $isCashPayment = $isCashPayment || $paymentType === 1;

        if (
          $paymentType !== 1
          && !empty($payment['number'])
        ) {
          $paymentReceipts[] = [
            'name' => $paymentName,
            'number' => (string) $payment['number']
          ];
        }
      }

      $paymentLabels = array_values(array_unique($paymentLabels));

      if (empty($paymentLabels)) {
        $paymentLabels[] = 'NO REGISTRADO';
      }

      /*
       * COMANDOS ESC/POS
       */
      $ESC = chr(27);
      $GS = chr(29);

      $printData = [];

      // Inicializar impresora y alinear al centro.
      $printData[] = $ESC . '@';
      $printData[] = $ESC . 'a' . chr(1);
      $printData[] = $ESC . 'E' . chr(1);
      $printData[] = "FILI\n";
      $printData[] = $ESC . 'E' . chr(0);
      $printData[] = "CENTRO DE COMPRAS\n";

      $printData[] = $ESC . 'a' . chr(0);
      $printData[] = $line . "\n";

      $documentLabel = $documentType === 1
        ? 'TICKET NRO: '
        : 'FACTURA NRO: ';

      $printData[] = $documentLabel
        . ($sale['number'] ?? $saleId)
        . "\n";

      $printData[] = $line . "\n";

      /*
       * CONDICION DE VENTA
       */
      $condition = match ((int) ($sale['type'] ?? 1)) {
        2 => 'CREDITO',
        3 => 'DEVOLUCION',
        4 => 'ESPERA',
        default => 'CONTADO'
      };

      $printData[] = 'CONDICION: ' . $condition . "\n";

      $dateText = trim(
        (string) ($sale['date'] ?? '')
        . ' '
        . (string) ($sale['time'] ?? '')
      );

      $printData[] = 'FECHA: ' . $dateText . "\n";
      $printData[] = 'CLIENTE: ' . $customerName . "\n";
      $printData[] = 'RUC: ' . $customerCi . "\n";
      $printData[] = 'PAGO: ' . implode('/', $paymentLabels) . "\n";

      $printData[] = $line . "\n";

      /*
       * ENCABEZADO DE ARTICULOS
       */
      $printData[] =
        $fitText('ARTICULO', $descriptionWidth)
        . str_pad('CANT', $quantityWidth, ' ', STR_PAD_LEFT)
        . str_pad('IVA', $ivaWidth, ' ', STR_PAD_LEFT)
        . str_pad('PRECIO', $priceWidth, ' ', STR_PAD_LEFT)
        . str_pad('MONTO', $totalWidth, ' ', STR_PAD_LEFT)
        . "\n";

      $printData[] = $line . "\n";

      /*
       * ARTICULOS: UNA LINEA POR PRODUCTO
       */
      foreach ($details as $detail) {
        $printData[] = $formatItem($detail);
      }

      $printData[] = $line . "\n";

      /*
       * SUBTOTALES E IVA
       */
      if ($subtotal5 > 0) {
        $printData[] = 'Subtotal 5% Gs. ' . $money($subtotal5) . "\n";
        $printData[] = 'IVA 5% Gs. ' . $money($iva5) . "\n";
      }

      if ($subtotal10 > 0) {
        $printData[] = 'Subtotal 10% Gs. ' . $money($subtotal10) . "\n";
        $printData[] = 'IVA 10% Gs. ' . $money($iva10) . "\n";
      }

      if ($subtotalExempt > 0) {
        $printData[] = 'Subtotal exenta Gs. ' . $money($subtotalExempt) . "\n";
      }

      $printData[] = $line . "\n";

      /*
       * TOTAL
       */
      $printData[] = $ESC . 'E' . chr(1);
      $printData[] = 'TOTAL Gs. ' . $money($total) . "\n";
      $printData[] = $ESC . 'E' . chr(0);

      /*
       * ENTREGA Y VUELTO SOLO PARA PAGOS EN EFECTIVO.
       */
      if ($isCashPayment) {
        $cashReceived = (float) ($sale['cash_received'] ?? 0);
        $cashChange = (float) ($sale['cash_change'] ?? 0);

        if ($cashReceived > 0) {
          $printData[] = 'ENTREGA Gs. ' . $money($cashReceived) . "\n";
          $printData[] = 'VUELTO Gs. ' . $money($cashChange) . "\n";
        }
      }

      /*
       * NUMEROS DE COMPROBANTES PARA PAGOS NO EFECTIVO.
       */
      foreach ($paymentReceipts as $receipt) {
        $printData[] = $receipt['name']
          . ' NRO: '
          . $receipt['number']
          . "\n";
      }

      /*
       * PIE DEL TICKET
       * Mostrar la deuda total solamente en ventas a crédito.
       */
      if ((int) $sale['type'] === 2) {
        $printData[] = $line . "\n";
        $printData[] = $ESC . 'E' . chr(1);
        $printData[] = 'TOTAL DEUDA Gs. ' . $money($customerDebt) . "\n";
        $printData[] = $ESC . 'E' . chr(0);
      }

      $printData[] = $line . "\n";
      $printData[] = $ESC . 'a' . chr(1);
      $printData[] = "Muchas gracias por su compra\n";

      if ($documentType === 1) {
        $printData[] = "No valido como factura\n";
      }

      $printData[] = "\n\n\n";

      /*
       * CORTE AUTOMATICO
       */
      // if ((int) ($printer['printer_auto_cut'] ?? 0) === 1) {
      //   $printData[] = $GS . 'V' . chr(0);
      // }
      // Avanzar el papel antes del corte
      if ((int) ($printer['printer_auto_cut'] ?? 0) === 1) {
        // Avanzar aproximadamente 4 cm
        $printData[] = "\n\n\n\n\n\n";

        // Cortar el papel
        $printData[] = "\x1D\x56\x00";
      }

      /*
       * RESPUESTA PARA JAVASCRIPT / QZ TRAY
       */
      return $this->response->setJSON([
        'status' => true,
        'printer' => $printer,
        'print_data' => $printData
      ]);

    } catch (\Throwable $e) {
      log_message(
        'error',
        'Error en Prints::print_data(): ' . $e->getMessage()
      );

      return $this->response->setJSON([
        'status' => false,
        'error' => 'ERROR AL PREPARAR LA IMPRESION',
        'detail' => ENVIRONMENT !== 'production'
          ? $e->getMessage()
          : null
      ]);
    }
  }


  public function print_pdf()
  {
    $saleId = 0;

    try {
      $values = $this->request->getPost();

      $saleId = (int) ($values['sale_id'] ?? 0);
      $documentType = (int) ($values['type'] ?? 0);

      if ($saleId <= 0 || !in_array($documentType, [1, 2], true)) {
        return $this->response->setJSON([
          'status' => false,
          'error' => 'DATOS DE IMPRESION NO VALIDOS'
        ]);
      }

      if (!class_exists(\Dompdf\Dompdf::class)) {
        return $this->response->setJSON([
          'status' => false,
          'error' => 'DOMPDF NO ESTA INSTALADO'
        ]);
      }

      $db = \Config\Database::connect();

      /*
       * CONFIGURACION DEL PAPEL
       *
       * Se utiliza el ancho imprimible configurado para
       * la impresora del dispositivo actual.
       */
      $printableMm = 72.00;
      $deviceId = (int) session('device');

      if ($deviceId > 0) {
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
      }

      /*
       * El ancho de página incluye 2 mm de margen
       * a cada lado del área imprimible.
       */
      $pageWidthMm = $printableMm + 4;

      /*
       * DATOS PRINCIPALES DE LA VENTA
       */
      $sale = $db->table('sales s')
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
        ->join('customers c', 'c.id = s.customer', 'left')
        ->join('users u', 'u.id = s.user', 'left')
        ->join('sucursals su', 'su.id = s.sucursal', 'left')
        ->join(
          'expedition_point ep',
          'ep.id = s.expedition_point',
          'left'
        )
        ->where('s.id', $saleId)
        ->get()
        ->getRowArray();

      if (!$sale) {
        return $this->response->setJSON([
          'status' => false,
          'error' => 'NO SE ENCONTRO LA VENTA'
        ]);
      }

      /*
       * DETALLES DE LA VENTA
       */
      $details = $db->table('sales_details sd')
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

      if (empty($details)) {
        return $this->response->setJSON([
          'status' => false,
          'error' => 'LA VENTA NO TIENE ARTICULOS'
        ]);
      }

      /*
       * PAGOS
       */
      $payments = $db->table('sales_payments sp')
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

      /*
       * FORMATO DE GUARANIES
       */
      $money = static function ($amount): string {
        return number_format((float) $amount, 0, ',', '.');
      };

      /*
       * ESCAPAR TEXTO HTML
       */
      $escape = static function ($value): string {
        return htmlspecialchars(
          (string) ($value ?? ''),
          ENT_QUOTES | ENT_SUBSTITUTE,
          'UTF-8'
        );
      };

      /*
       * DATOS DEL DOCUMENTO
       */
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
       * SUBTOTALES POR TIPO DE IVA
       *
       * Se considera que los precios de los detalles
       * ya incluyen el IVA.
       */
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

      $iva5 = round($subtotal5 / 21);
      $iva10 = round($subtotal10 / 11);

      /*
       * FILAS DE PRODUCTOS
       */
      $itemsHtml = '';

      foreach ($details as $item) {
        $description = trim(
          (string) (
            $item['product_description']
            ?: $item['product']
          )
        );

        $ivaLabel = match ((int) ($item['type_iva'] ?? 3)) {
          1 => '10%',
          2 => '5%',
          default => 'Exenta'
        };

        $itemsHtml .= '
                <tr>
                    <td class="description">' .
          $escape($description) .
          '</td>
                    <td class="center quantity">' .
          $escape($money($item['cant'] ?? 0)) .
          '</td>
                    <td class="center iva">' .
          $escape($ivaLabel) .
          '</td>
                    <td class="right price">' .
          $escape($money($item['unit_price'] ?? 0)) .
          '</td>
                    <td class="right amount">' .
          $escape($money($item['total_price'] ?? 0)) .
          '</td>
                </tr>';
      }

      /*
       * DESGLOSE DE IVA
       */
      $ivaHtml = '';

      if ($subtotal5 > 0) {
        $ivaHtml .= '
                <tr>
                    <td>Subtotal 5%</td>
                    <td class="right">Gs. ' .
          $money($subtotal5) .
          '</td>
                </tr>
                <tr>
                    <td>IVA 5%</td>
                    <td class="right">Gs. ' .
          $money($iva5) .
          '</td>
                </tr>';
      }

      if ($subtotal10 > 0) {
        $ivaHtml .= '
                <tr>
                    <td>Subtotal 10%</td>
                    <td class="right">Gs. ' .
          $money($subtotal10) .
          '</td>
                </tr>
                <tr>
                    <td>IVA 10%</td>
                    <td class="right">Gs. ' .
          $money($iva10) .
          '</td>
                </tr>';
      }

      if ($subtotalExempt > 0) {
        $ivaHtml .= '
                <tr>
                    <td>Subtotal exenta</td>
                    <td class="right">Gs. ' .
          $money($subtotalExempt) .
          '</td>
                </tr>';
      }

      /*
       * FORMAS DE PAGO
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
                    <td class="right">Gs. ' .
          $money($payment['amount'] ?? 0) .
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
                        <td colspan="2">
                            Comprobante Nro.: ' .
            $escape($payment['number']) . '
                        </td>
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
       *
       * Mostrar solamente cuando hay un pago en efectivo
       * y existe un importe recibido.
       */
      $cashHtml = '';

      if (
        $isCashPayment
        && (float) ($sale['cash_received'] ?? 0) > 0
      ) {
        $cashHtml = '
                <div class="cash-row">
                    <span>ENTREGA</span>
                    <span>Gs. ' .
          $money($sale['cash_received']) .
          '</span>
                </div>
                <div class="cash-row">
                    <span>VUELTO</span>
                    <span>Gs. ' .
          $money($sale['cash_change'] ?? 0) .
          '</span>
                </div>';
      }

      /*
       * ALTURA DINAMICA DEL PAPEL
       *
       * Se deja espacio adicional para los encabezados,
       * los productos, los pagos y el pie.
       */
      $heightMm = 100
        + (count($details) * 7)
        + (count($payments) * 6)
        + (count($paymentReceipts) * 5);

      /*
       * HTML DEL PDF
       */
      $html = '
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

                .info {
                    width: 100%;
                    table-layout: fixed;
                    border-collapse: collapse;
                    margin: 0;
                }

                .info td {
                    border: 0;
                    padding: 1px 0;
                    vertical-align: top;
                    overflow-wrap: anywhere;
                }

                .info .label {
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
                    width: 34%;
                    text-align: left;
                }

                .quantity {
                    width: 12%;
                }

                .iva {
                    width: 12%;
                }

                .price {
                    width: 21%;
                }

                .amount {
                    width: 21%;
                }

                .center {
                    text-align: center;
                }

                .right {
                    text-align: right;
                }

                .summary {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 5px;
                    table-layout: fixed;
                }

                .summary td {
                    border: 0;
                    padding: 2px 0;
                    overflow-wrap: anywhere;
                }

                .total {
                    font-size: 11px;
                    font-weight: bold;
                    border-top: 1px solid #000 !important;
                    padding-top: 5px !important;
                }

                .cash-row {
                    display: block;
                    width: 100%;
                    margin-top: 3px;
                    font-weight: bold;
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
                <p class="document-title">' .
        $escape($documentTitle) .
        '</p>
            </div>

            <div class="separator"></div>

            <table class="info">
                <tr>
                    <td class="label">Venta Nro.:</td>
                    <td>' . $escape($sale['number'] ?? $saleId) . '</td>
                </tr>

                <tr>
                    <td class="label">Condición:</td>
                    <td>' . $escape($condition) . '</td>
                </tr>

                <tr>
                    <td class="label">Fecha:</td>
                    <td>' .
        $escape($sale['date'] ?? '') . ' ' .
        $escape($sale['time'] ?? '') .
        '</td>
                </tr>

                <tr>
                    <td class="label">Sucursal:</td>
                    <td>' .
        $escape($sale['sucursal_name'] ?? '') .
        '</td>
                </tr>

                <tr>
                    <td class="label">Punto expedición:</td>
                    <td>' .
        $escape($sale['expedition_point_code'] ?? '') .
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
                    <td>' .
        $escape($sale['user_name'] ?? '') .
        '</td>
                </tr>

                <tr>
                    <td class="label">Pago:</td>
                    <td>' .
        $escape(implode(' / ', $paymentLabels)) .
        '</td>
                </tr>
            </table>

            <div class="separator"></div>

            <table class="products">
                <thead>
                    <tr>
                        <th class="description">ARTICULO</th>
                        <th class="quantity center">CANT.</th>
                        <th class="iva center">IVA</th>
                        <th class="price right">PRECIO</th>
                        <th class="amount right">MONTO</th>
                    </tr>
                </thead>

                <tbody>' .
        $itemsHtml .
        '</tbody>
            </table>

            <div class="separator"></div>

            <table class="summary">
                <tbody>' .
        $ivaHtml . '

                    <tr>
                        <td class="total">TOTAL Gs.</td>
                        <td class="right total">' .
        $money($sale['total_price'] ?? 0) .
        '</td>
                    </tr>
                </tbody>
            </table>

            ' . $cashHtml . '

            <div class="separator"></div>

            <strong>DETALLE DE PAGOS</strong>

            <table class="summary">
                <tbody>' .
        $paymentsHtml .
        '</tbody>
            </table>

            <div class="footer">
                <p>Muchas gracias por su compra</p>' .
        ($documentType === 1
          ? '<p>No válido como factura</p>'
          : '') . '
            </div>

        </body>
        </html>';

      /*
       * GENERAR PDF
       */
      $options = new \Dompdf\Options();

      $options->set('isRemoteEnabled', false);
      $options->set('defaultFont', 'DejaVu Sans');
      $options->set('isHtml5ParserEnabled', true);

      $pdf = new \Dompdf\Dompdf($options);

      $pdf->loadHtml($html, 'UTF-8');

      /*
       * Dompdf utiliza puntos:
       * 1 mm = 2.83464567 puntos.
       */
      $widthPoints = $pageWidthMm * 2.83464567;
      $heightPoints = $heightMm * 2.83464567;

      $pdf->setPaper([
        0,
        0,
        $widthPoints,
        $heightPoints
      ]);

      $pdf->render();

      $pdfBase64 = base64_encode($pdf->output());

      return $this->response->setJSON([
        'status' => true,
        'filename' => 'venta_' . $saleId . '.pdf',
        'pdf' => $pdfBase64,
        'printable_mm' => $printableMm
      ]);

    } catch (\Throwable $e) {
      log_message(
        'error',
        'Error al generar PDF de venta ' .
        $saleId . ': ' . $e->getMessage()
      );

      return $this->response->setStatusCode(500)->setJSON([
        'status' => false,
        'error' => 'NO SE PUDO GENERAR EL PDF',
        'detail' => ENVIRONMENT !== 'production'
          ? $e->getMessage()
          : null
      ]);
    }
  }

  public function print_invoice_two()
  {
    $saleId = (int) $this->request->getPost('sale_id');
    $type = (int) $this->request->getPost('type');

    if ($saleId <= 0 || $type !== 2) {
      return $this->response->setJSON([
        'status' => false,
        'error' => 'DATOS DE FACTURA NO VÁLIDOS'
      ]);
    }

    try {
      $service = new \App\Services\PrintService();

      $result = $service->print_invoice_two($saleId);

      if (!$result['status']) {
        return $this->response->setJSON($result);
      }

      return $this->response->setJSON($result);

    } catch (\Throwable $e) {
      log_message(
        'error',
        'Error al imprimir factura | Venta: ' . $saleId .
        ' | Archivo: ' . $e->getFile() .
        ' | Línea: ' . $e->getLine() .
        ' | Error: ' . $e->getMessage()
      );

      return $this->response
        ->setStatusCode(500)
        ->setJSON([
          'status' => false,
          'error' => 'NO SE PUDO GENERAR LA FACTURA',
          'detail' => ENVIRONMENT !== 'production'
            ? $e->getMessage()
            : null
        ]);
    }
  }


}
