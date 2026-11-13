<?php

namespace App\Controllers\Prints;

use App\Controllers\BaseController;

class GenerateInvoice extends BaseController
{

  /*
   * IMPRIMIR FACTURA GUARDADA
   */

  public function print()
  {
    $invoiceId = (int) $this->request->getPost('invoice_id');

    if ($invoiceId <= 0) {
      return $this->response
        ->setStatusCode(400)
        ->setJSON([
          'status' => false,
          'error' => 'ID DE FACTURA NO VÁLIDO',
        ]);
    }

    try {
      $service = new \App\Services\GenerateInvoiceService();

      $result = $service->generate($invoiceId);

      return $this->response->setJSON($result);

    } catch (\Throwable $e) {
      log_message(
        'error',
        'GenerateInvoice::print | Factura: ' . $invoiceId .
        ' | Archivo: ' . $e->getFile() .
        ' | Línea: ' . $e->getLine() .
        ' | Error: ' . $e->getMessage()
      );

      return $this->response
        ->setStatusCode(500)
        ->setJSON([
          'status' => false,
          'error' => 'ERROR AL GENERAR LA FACTURA',
        ]);
    }
  }


  /*
   * PDF DE RESPALDO DE UNA FACTURA GUARDADA
   */
  public function pdf()
  {
    $invoiceId = (int) $this->request->getPost('invoice_id');

    if ($invoiceId <= 0) {
      return $this->response
        ->setStatusCode(400)
        ->setJSON([
          'status' => false,
          'error' => 'ID DE FACTURA NO VÁLIDO',
        ]);
    }

    try {
      $service = new \App\Services\GenerateInvoiceService();

      // El PDF de respaldo no exige impresora.
      $result = $service->generate($invoiceId, false);

      if (empty($result['status']) || empty($result['pdf'])) {
        return $this->response
          ->setStatusCode(500)
          ->setJSON($result);
      }

      return $this->response->setJSON([
        'status' => true,
        'invoice_id' => $invoiceId,
        'filename' => $result['filename'],
        'pdf' => $result['pdf'],
      ]);
    } catch (\Throwable $e) {
      log_message(
        'error',
        'Error al generar PDF de factura guardada | ID: ' .
        $invoiceId . ' | ' . $e->getMessage()
      );

      return $this->response
        ->setStatusCode(500)
        ->setJSON([
          'status' => false,
          'error' => 'NO SE PUDO GENERAR EL PDF',
        ]);
    }
  }



}
