<?php

namespace App\Controllers\Prints;

use App\Controllers\BaseController;

class Invoice extends BaseController
{
  /*
   * PREPARAR IMPRESION  O FACTURA
   */
  public function print()
  {
    $saleId = (int) $this->request->getPost('sale_id');
    $type = (int) $this->request->getPost('type');
    $receiptType = (int) $this->request->getPost('receipt_type');

    if (
      $saleId <= 0 ||
      $type !== 2 ||
      !in_array($receiptType, [1, 2], true)
    ) {
      return $this->response->setJSON([
        'status' => false,
        'error' => 'DATOS DE FACTURA NO VÁLIDOS',
      ]);
    }

    try {
      $service = new \App\Services\InvoiceService();

      $result = $service->print_invoice_two(
        $saleId,
        true,
        $receiptType
      );

      return $this->response->setJSON($result);

    } catch (\Throwable $e) {
      log_message(
        'error',
        'Error al imprimir factura | Venta: ' . $saleId .
        ' | Error: ' . $e->getMessage()
      );

      return $this->response
        ->setStatusCode(500)
        ->setJSON([
          'status' => false,
          'error' => 'NO SE PUDO GENERAR LA FACTURA',
          'detail' => ENVIRONMENT !== 'production'
            ? $e->getMessage()
            : null,
        ]);
    }
  }


  /*
   * GENERAR PDF DE RESPALDO
   */

  public function pdf()
  {
    $saleId = (int) $this->request->getPost('sale_id');
    $type = (int) $this->request->getPost('type');

    if ($saleId <= 0 || $type !== 2) {
      return $this->response->setStatusCode(400)->setJSON([
        'status' => false,
        'error' => 'DATOS DE FACTURA NO VÁLIDOS'
      ]);
    }

    try {
      $receiptType = (int) $this->request->getPost('receipt_type');

      if (!in_array($receiptType, [1, 2], true)) {
        return $this->response->setStatusCode(400)->setJSON([
          'status' => false,
          'error' => 'TIPO DE COMPROBANTE NO VÁLIDO'
        ]);
      }

      $service = new \App\Services\InvoiceService();

      // Generar el PDF sin exigir una impresora configurada.
      // receipt_type: 1 = Interno, 2 = Fiscal.
      $result = $service->print_invoice_two(
        $saleId,
        false,
        $receiptType
      );

      if (empty($result['status']) || empty($result['pdf'])) {
        return $this->response->setStatusCode(500)->setJSON([
          'status' => false,
          'error' => $result['error'] ?? 'NO SE PUDO GENERAR EL PDF'
        ]);
      }

      return $this->response->setJSON([
        'status' => true,
        'filename' => $result['filename'] ?? 'factura_' . $saleId . '.pdf',
        'pdf' => $result['pdf']
      ]);
    } catch (\Throwable $e) {
      log_message(
        'error',
        'Error al generar PDF de factura | Venta: ' . $saleId .
        ' | Archivo: ' . $e->getFile() .
        ' | Línea: ' . $e->getLine() .
        ' | Error: ' . $e->getMessage()
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



}
