<?php

namespace App\Controllers\Prints;

use App\Controllers\BaseController;
use App\Services\TicketService;
use App\Services\TicketPdfService;

class Ticket extends BaseController
{
  /*
   * PREPARAR IMPRESION DEL TICKET O FACTURA
   */
  public function print()
  {
    try {
      $saleId = (int) $this->request->getPost('sale_id');
      $documentType = (int) $this->request->getPost('type');
      $deviceId = (int) session('device');

      $ticketService = new TicketService();

      $result = $ticketService->prepareTicket(
        $saleId,
        $documentType,
        $deviceId
      );

      return $this->response->setJSON($result);

    } catch (\Throwable $e) {
      log_message(
        'error',
        'Error en Prints\Ticket::print(): ' . $e->getMessage()
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


  /*
   * GENERAR PDF DE RESPALDO
   */
  public function pdf()
  {
    $saleId = 0;

    try {
      $saleId = (int) $this->request->getPost('sale_id');
      $documentType = (int) $this->request->getPost('type');
      $deviceId = (int) session('device');

      if (
        $saleId <= 0
        || !in_array($documentType, [1, 2], true)
      ) {
        return $this->response
          ->setStatusCode(400)
          ->setJSON([
            'status' => false,
            'error' => 'DATOS DE IMPRESION NO VALIDOS'
          ]);
      }

      $pdfService = new TicketPdfService();

      $result = $pdfService->generatePdf(
        $saleId,
        $documentType,
        $deviceId
      );

      return $this->response->setJSON($result);

    } catch (\Throwable $e) {
      log_message(
        'error',
        'Error al generar PDF de venta '
        . $saleId . ': '
        . $e->getMessage()
      );

      return $this->response
        ->setStatusCode(500)
        ->setJSON([
          'status' => false,
          'error' => 'NO SE PUDO GENERAR EL PDF',
          'detail' => ENVIRONMENT !== 'production'
            ? $e->getMessage()
            : null
        ]);
    }
  }

}
