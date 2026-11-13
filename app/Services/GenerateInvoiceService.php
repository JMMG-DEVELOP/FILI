<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;
use Throwable;

class GenerateInvoiceService
{
  private BaseConnection $db;

  public function __construct()
  {
    $this->db = db_connect();
  }

  /*
   * PROCESO PRINCIPAL
   *
   * Obtiene una factura guardada y genera su documento
   * utilizando el mismo diseño de InvoiceService.
   */
  public function generate(
    int $invoiceId,
    bool $requirePrinter = true
  ): array {
    try {
      if ($invoiceId <= 0) {
        return $this->error('ID DE FACTURA NO VÁLIDO');
      }

      // Obtener los datos de la factura guardada.
      $invoice = $this->getInvoice($invoiceId);

      if (!$invoice) {
        return $this->error('NO SE ENCONTRÓ LA FACTURA');
      }

      // Recuperar todos los artículos de sus ventas asociadas.
      $details = $this->getInvoiceDetails($invoiceId);

      if (empty($details)) {
        return $this->error(
          'LA FACTURA NO TIENE PRODUCTOS ASOCIADOS'
        );
      }

      // Obtener el punto de expedición usando el número guardado.
      $point = $this->getExpeditionPoint(
        $invoice['number'],
        (int) $invoice['sucursal']
      );

      if (!$point) {
        return $this->error(
          'NO SE ENCONTRÓ EL PUNTO DE EXPEDICIÓN'
        );
      }

      /*
       * Adaptar los datos de invoices al formato que espera
       * el formateador original de InvoiceService.
       *
       * No se cambia el número guardado en invoices.
       */
      $document = [
        'id' => (int) $invoice['id'],
        'number' => $invoice['number'],
        'date' => $invoice['date'],
        'time' => $invoice['time'],
        'sale_type' => 1,
        'total_price' => (float) $invoice['mount'],
        'customer' => (int) $invoice['customer'],
        'sucursal' => (int) $invoice['sucursal'],
        'expedition_point' => (int) $point['id'],
        'expedition_point_code' => $point['code'],
        'customer_name' => $invoice['customer_name'],
        'customer_ci' => $invoice['customer_ci'],
        'customer_address' => $invoice['customer_address'],
        'sucursal_name' => $invoice['sucursal_name'],
      ];

      /*
       * Delegar el diseño, el IVA, las páginas, las letras,
       * la generación PDF y la actualización de secuencia
       * al servicio original.
       */
      $service = new InvoiceService();

      $result = $service->print_invoice_two_from_data(
        $document,
        $details,
        $requirePrinter,
        'factura_' . $invoiceId . '.pdf'
      );

      if (empty($result['status'])) {
        return $result;
      }

      $result['invoice_id'] = $invoiceId;
      $result['number'] = $invoice['number'];

      return $result;
    } catch (Throwable $e) {
      log_message(
        'error',
        'GenerateInvoiceService::generate | Factura: ' .
        $invoiceId .
        ' | Archivo: ' . $e->getFile() .
        ' | Línea: ' . $e->getLine() .
        ' | Error: ' . $e->getMessage()
      );

      return $this->error(
        'ERROR AL GENERAR LA FACTURA'
      );
    }
  }

  /*
   * DATOS PRINCIPALES DE LA FACTURA
   */
  private function getInvoice(int $invoiceId): ?array
  {
    $invoice = $this->db->table('invoices i')
      ->select([
        'i.id',
        'i.number',
        'i.mount',
        'i.date',
        'i.time',
        'i.user',
        'i.customer',
        'i.status',
        'i.sucursal',
        'c.name AS customer_name',
        'c.ci AS customer_ci',
        'c.address AS customer_address',
        'su.name AS sucursal_name',
      ])
      ->join(
        'customers c',
        'c.id = i.customer',
        'left'
      )
      ->join(
        'sucursals su',
        'su.id = i.sucursal',
        'left'
      )
      ->where('i.id', $invoiceId)
      ->get()
      ->getRowArray();

    return $invoice ?: null;
  }

  /*
   * ARTÍCULOS DE TODAS LAS VENTAS ASOCIADAS A LA FACTURA
   *
   * Mantiene los mismos nombres de campos que utiliza
   * InvoiceService::print_invoice_two_formatter().
   */
  private function getInvoiceDetails(int $invoiceId): array
  {
    return $this->db->table('invoices_sales ins')
      ->select([
        'sd.id',
        'sd.product',
        'sd.cant',
        'sd.unit_price',
        'sd.total_price',
        'sd.type_iva',
        'sd.total_iva',
        'p.description AS product_description',
      ])
      ->join(
        'sales_details sd',
        'sd.sales = ins.sales',
        'inner'
      )
      ->join(
        'products p',
        'p.code = sd.product',
        'left'
      )
      ->where('ins.invoice', $invoiceId)
      ->orderBy('ins.id', 'ASC')
      ->orderBy('sd.id', 'ASC')
      ->get()
      ->getResultArray();
  }

  /*
   * PUNTO DE EXPEDICIÓN
   *
   * Ejemplo: 001 003 0000049
   * Sucursal: 001
   * Punto: 003
   * Número: 0000049
   */
  private function getExpeditionPoint(
    string $number,
    int $sucursalId
  ): ?array {
    $parts = preg_split('/\s+/', trim($number));

    if (
      count($parts) !== 3 ||
      !ctype_digit($parts[0]) ||
      !ctype_digit($parts[1]) ||
      !ctype_digit($parts[2])
    ) {
      return null;
    }

    $point = $this->db->table('expedition_point')
      ->select(['id', 'code', 'sucursal'])
      ->where('code', $parts[1])
      ->where('sucursal', $sucursalId)
      ->get()
      ->getRowArray();

    return $point ?: null;
  }

  private function error(string $message): array
  {
    return [
      'status' => false,
      'error' => $message,
    ];
  }
}
