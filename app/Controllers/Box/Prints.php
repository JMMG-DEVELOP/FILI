<?php
namespace App\Controllers\Box;

use App\Controllers\BaseController;
use App\Models\Printers\DevicesPrintersModel;
use App\Models\Customer\CustomersCreditsModel;
use App\Services\TicketPdfService;
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
