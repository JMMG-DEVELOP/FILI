<?php
namespace App\Controllers\Invoices;

use App\Controllers\BaseController;


class Create extends BaseController
{

  public function form_open()
  {
    $html = view(
      'invoices/create/home',
    );

    return $this->finish(
      true,
      'VENTAS CARGADAS CORRECTAMENTE',
      $html
    );

  }

  public function sales_panel()
  {
    $html = view(
      'invoices/create/list',
    );

    return $this->finish(
      true,
      'VENTAS CARGADAS CORRECTAMENTE',
      $html
    );

  }
  public function sales_detail_panel()
  {
    $id = (int) $this->request->getPost('id');

    if ($id <= 0) {
      return $this->finish(
        false,
        'NO SE RECIBIÓ UN ID DE VENTA VÁLIDO'
      );
    }

    try {
      $model = new \App\Models\Box\SalesDetailsModel();

      $details = $model->get_id($id);

      if (empty($details)) {
        return $this->finish(
          false,
          'LA VENTA NO TIENE DETALLES O NO EXISTE'
        );
      }

      $html = view('invoices/create/detail', [
        'details' => $details,
        'sale_id' => $id
      ]);

      return $this->finish(
        true,
        'DETALLES CARGADOS CORRECTAMENTE',
        $html
      );
    } catch (\Throwable $e) {
      log_message(
        'error',
        'ERROR AL CARGAR DETALLES DE VENTA: ' .
        $e->getMessage() .
        ' | Archivo: ' . $e->getFile() .
        ' | Línea: ' . $e->getLine()
      );

      return $this->finish(
        false,
        'ERROR AL CARGAR LOS DETALLES DE LA VENTA'
      );
    }
  }
  public function sales_datatable()
  {
    try {

      $model = new \App\Models\Box\SalesModel();

      $post = $this->request->getPost();

      $result = $model->getInvoiceDatatable($post);

      return $this->response->setJSON(
        array_merge($result, [
          'csrfName' => csrf_token(),
          'csrfHash' => csrf_hash()
        ])
      );

    } catch (\Throwable $e) {

      log_message(
        'error',
        'ERROR AL CARGAR VENTAS PARA BOLETAS: ' .
        $e->getMessage() .
        ' | Archivo: ' . $e->getFile() .
        ' | Línea: ' . $e->getLine()
      );

      return $this->response->setJSON([
        'draw' => (int) (
          $this->request->getPost('draw') ?? 1
        ),
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'status' => false,
        'msg' => 'NO SE PUDIERON CARGAR LAS VENTAS',
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash()
      ]);
    }
  }
  public function sales_add_invoice()
  {
    $id = (int) $this->request->getPost('id');

    if ($id <= 0) {
      return $this->finish(
        false,
        'ID DE VENTA NO VÁLIDO'
      );
    }

    try {
      $salesModel = new \App\Models\Box\SalesModel();
      $detailsModel = new \App\Models\Box\SalesDetailsModel();

      $sale = $salesModel->find($id);

      if (
        !$sale ||
        (int) $sale['type'] !== 1 ||
        (int) $sale['invoice_type'] !== 1
      ) {
        return $this->finish(
          false,
          'LA VENTA NO EXISTE O NO ES VÁLIDA PARA FACTURAR'
        );
      }

      $details = $detailsModel->getInvoiceDetails($id);

      if (empty($details)) {
        return $this->finish(
          false,
          'LA VENTA NO TIENE DETALLES'
        );
      }

      return $this->finish(
        true,
        'VENTA CARGADA CORRECTAMENTE',
        '',
        [
          'id' => (int) $sale['id'],
          'total_price' => (float) $sale['total_price'],
          'details' => $details,
          'items' => count($details)
        ]
      );
    } catch (\Throwable $e) {
      log_message(
        'error',
        'ERROR AL AGREGAR VENTA A FACTURA: ' .
        $e->getMessage() .
        ' | Archivo: ' . $e->getFile() .
        ' | Línea: ' . $e->getLine()
      );

      return $this->finish(
        false,
        'ERROR AL CARGAR LA VENTA'
      );
    }
  }

public function save_invoice()
{
    $db = \Config\Database::connect();

    $values = $this->request->getPost();

    $customerId = (int) ($values['customer_id'] ?? 0);
    $sucursalId = (int) ($values['select_sucursal'] ?? 0);
    $expeditionPointId = (int) ($values['expedition_point_id'] ?? 0);
    $sequenceId = (int) ($values['sequence_id'] ?? 0);
    $invoiceNumber = trim((string) ($values['invoice_number'] ?? ''));

    $userId = (int) (session('id') ?? session('user') ?? 0);

    $saleIds = $values['sales'] ?? [];

    if (!is_array($saleIds)) {
        return $this->finish(false, 'LAS VENTAS RECIBIDAS NO SON VÁLIDAS');
    }

    $saleIds = array_values(array_unique(array_filter(
        array_map('intval', $saleIds),
        static fn ($id) => $id > 0
    )));

    if ($customerId <= 0) {
        return $this->finish(false, 'DEBE SELECCIONAR UN CLIENTE');
    }

    if ($sucursalId <= 0) {
        return $this->finish(false, 'DEBE SELECCIONAR UNA SUCURSAL');
    }

    if ($expeditionPointId <= 0 || $sequenceId <= 0 || $invoiceNumber === '') {
        return $this->finish(false, 'LOS DATOS DEL PUNTO DE EXPEDICIÓN SON INCORRECTOS');
    }

    if ($userId <= 0) {
        return $this->finish(false, 'LA SESIÓN DEL USUARIO NO ES VÁLIDA');
    }

    if (empty($saleIds)) {
        return $this->finish(false, 'DEBE AGREGAR AL MENOS UNA VENTA');
    }

    // Verificar que el número tenga el formato esperado: 001 003 0000005.
    if (!preg_match('/^\d{3}\s+\d{3}\s+\d{7}$/', $invoiceNumber)) {
        return $this->finish(false, 'EL FORMATO DEL NÚMERO DE FACTURA NO ES VÁLIDO');
    }

    try {
        $db->transBegin();

        /*
         * 1. Validar cliente.
         */
        $customer = $db->table('customers')
            ->select('id')
            ->where('id', $customerId)
            ->get()
            ->getRowArray();

        if (!$customer) {
            $db->transRollback();

            return $this->finish(false, 'EL CLIENTE SELECCIONADO NO EXISTE');
        }

        /*
         * 2. Validar sucursal.
         */
        $branch = $db->table('sucursals')
            ->select('id')
            ->where('id', $sucursalId)
            ->get()
            ->getRowArray();

        if (!$branch) {
            $db->transRollback();

            return $this->finish(false, 'LA SUCURSAL SELECCIONADA NO EXISTE');
        }

        /*
         * 3. Validar punto de expedición.
         */
        $point = $db->table('expedition_point')
            ->select('id, sucursal')
            ->where('id', $expeditionPointId)
            ->where('sucursal', $sucursalId)
            ->get()
            ->getRowArray();

        if (!$point) {
            $db->transRollback();

            return $this->finish(
                false,
                'EL PUNTO DE EXPEDICIÓN NO CORRESPONDE A LA SUCURSAL'
            );
        }

        /*
         * 4. Bloquear la secuencia y obtener el último número guardado.
         */
        $sequence = $db->query(
            'SELECT id, last_number, expedition_point
             FROM invoice_sequence
             WHERE id = ?
               AND expedition_point = ?
             FOR UPDATE',
            [$sequenceId, $expeditionPointId]
        )->getRowArray();

        if (!$sequence) {
            $db->transRollback();

            return $this->finish(
                false,
                'LA SECUENCIA NO CORRESPONDE AL PUNTO DE EXPEDICIÓN'
            );
        }

        /*
         * 5. Calcular el siguiente número desde la base de datos.
         *
         * Ejemplo:
         * last_number = 5
         * siguiente número = 6
         *
         * No confiamos en sequence_number enviado por JavaScript.
         */
        $lastNumber = (int) $sequence['last_number'];
        $expectedNumber = $lastNumber + 1;

        // Extraer los 7 dígitos finales del número de factura.
        $numberParts = preg_replace('/\D/', '', $invoiceNumber);
        $invoiceSequenceNumber = (int) substr($numberParts, -7);

        if ($invoiceSequenceNumber !== $expectedNumber) {
            $db->transRollback();

            log_message(
                'warning',
                'SECUENCIA DE FACTURA NO COINCIDE. '
                . 'Último guardado: ' . $lastNumber
                . ' | Esperado: ' . $expectedNumber
                . ' | Número recibido: ' . $invoiceNumber
                . ' | Sufijo recibido: ' . $invoiceSequenceNumber
            );

            return $this->finish(
                false,
                'EL NÚMERO DE FACTURA NO ES EL SIGUIENTE. ACTUALICE EL PUNTO DE EXPEDICIÓN'
            );
        }

        /*
         * 6. Comprobar que el número completo no esté registrado.
         */
        $existingInvoice = $db->table('invoices')
            ->select('id')
            ->where('number', $invoiceNumber)
            ->where('sucursal', $sucursalId)
            ->get()
            ->getRowArray();

        if ($existingInvoice) {
            $db->transRollback();

            return $this->finish(
                false,
                'EL NÚMERO DE FACTURA YA EXISTE. ACTUALICE LA SECUENCIA'
            );
        }

        /*
         * 7. Obtener las ventas válidas.
         */
        $sales = $db->table('sales')
            ->select('id, total_price, type, invoice_type, status')
            ->whereIn('id', $saleIds)
            ->where('type', 1)
            ->where('invoice_type', 1)
            ->where('status', 1)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        if (count($sales) !== count($saleIds)) {
            $db->transRollback();

            return $this->finish(
                false,
                'UNA O MÁS VENTAS NO EXISTEN, ESTÁN ANULADAS O NO SON VÁLIDAS PARA FACTURAR'
            );
        }

        /*
         * 8. Impedir facturar ventas que ya estén asociadas.
         */
        $alreadyInvoiced = $db->table('invoices_sales')
            ->select('sales')
            ->whereIn('sales', $saleIds)
            ->get()
            ->getResultArray();

        if (!empty($alreadyInvoiced)) {
            $db->transRollback();

            return $this->finish(
                false,
                'UNA O MÁS VENTAS YA ESTÁN ASOCIADAS A UNA FACTURA'
            );
        }

        /*
         * 9. Calcular el total real desde las ventas.
         */
        $total = 0;

        foreach ($sales as $sale) {
            $total += (float) $sale['total_price'];
        }

        $total = round($total, 2);

        /*
         * 10. Guardar la factura.
         */
        $invoiceModel = new \App\Models\Invoices\InvoicesModel();

        $invoiceId = $invoiceModel->add([
            'number'   => $invoiceNumber,
            'mount'    => $total,
            'date'     => date('Y-m-d'),
            'time'     => date('H:i:s'),
            'user'     => $userId,
            'customer' => $customerId,
            'status'   => 1,
            'sucursal' => $sucursalId
        ]);

        if (!$invoiceId) {
            $db->transRollback();

            return $this->finish(
                false,
                'NO SE PUDO GUARDAR LA FACTURA. REVISE EL LOG DEL SISTEMA'
            );
        }

        /*
         * 11. Guardar las ventas relacionadas con la factura.
         */
        $invoiceSales = [];

        foreach ($saleIds as $saleId) {
            $invoiceSales[] = [
                'invoice' => $invoiceId,
                'sales'   => $saleId
            ];
        }

        $inserted = $db->table('invoices_sales')
            ->insertBatch($invoiceSales);

        if ($inserted === false) {
            $db->transRollback();

            log_message(
                'error',
                'ERROR AL ASOCIAR VENTAS A FACTURA '
                . $invoiceId
                . ' | DB: '
                . json_encode($db->error(), JSON_UNESCAPED_UNICODE)
            );

            return $this->finish(
                false,
                'NO SE PUDIERON ASOCIAR LAS VENTAS A LA FACTURA'
            );
        }

        /*
         * 12. Actualizar el último número utilizado.
         */
        $updated = $db->table('invoice_sequence')
            ->where('id', $sequenceId)
            ->where('expedition_point', $expeditionPointId)
            ->update([
                'last_number' => $expectedNumber
            ]);

        if ($updated === false) {
            $db->transRollback();

            return $this->finish(
                false,
                'NO SE PUDO ACTUALIZAR LA SECUENCIA DE FACTURACIÓN'
            );
        }

        /*
         * 13. Verificar y confirmar la transacción.
         */
        if ($db->transStatus() === false) {
            $db->transRollback();

            return $this->finish(
                false,
                'OCURRIÓ UN ERROR AL GUARDAR LA FACTURA'
            );
        }

        $db->transCommit();

        /*
         * 14. Retornar el ID para JavaScript e impresión.
         */
        return $this->finish(
            true,
            'FACTURA GUARDADA CORRECTAMENTE',
            '',
            [
                'invoice_id' => (int) $invoiceId,
                'number'     => $invoiceNumber,
                'mount'      => $total,
                'sales'      => count($saleIds)
            ]
        );

    } catch (\Throwable $e) {
        $db->transRollback();

        log_message(
            'error',
            'ERROR AL GUARDAR FACTURA: '
            . $e->getMessage()
            . ' | Archivo: ' . $e->getFile()
            . ' | Línea: ' . $e->getLine()
        );

        return $this->finish(
            false,
            'ERROR INTERNO AL GUARDAR LA FACTURA'
        );
    }
}

  public function finish($status, $msg, $html = '', $data = '')
  {
    return $this->response->setJSON([
      'status' => $status,
      'msg' => $msg,
      'html' => $html,
      'data' => $data,
      'csrfName' => csrf_token(),
      'csrfHash' => csrf_hash()
    ]);
  }


}
