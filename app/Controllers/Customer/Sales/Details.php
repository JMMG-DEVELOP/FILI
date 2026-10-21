<?php
namespace App\Controllers\Customer\Sales;
use App\Controllers\BaseController;

use App\Models\Box\SalesDetailsModel;
use App\Models\Customer\CustomersCreditsDetailsModel;
class Details extends BaseController
{
    protected $Model;
    protected $Status;

    public function __construct()
    {
        $this->Model = new SalesDetailsModel();
    }

    public function panel_load()
    {
        $id = $this->request->getPost('id');

        if (empty($id) || !is_numeric($id)) {
            return $this->finish(
                false,
                'NO SE RECIBIÓ UN CLIENTE VÁLIDO'
            );
        }

        $values = $this->Model->get_id($id);
        $customerId = !empty($values)
            ? (int) ($values[0]['customer_id'] ?? 0)
            : 0;
        $data = [
            'values' => $values,
            'customer' => $customerId
        ];
        $html = view(
            'customer/modules/sales/details',
            $data
        );

        return $this->finish(
            true,
            'VENTAS CARGADAS CORRECTAMENTE',
            $html
        );
    }

    public function anoter_panel_load()
    {
        $customerId = $this->request->getPost('id');

        if (
            empty($customerId) ||
            !is_numeric($customerId) ||
            (int) $customerId <= 0
        ) {
            return $this->finish(
                false,
                'NO SE RECIBIÓ UN CLIENTE VÁLIDO'
            );
        }

        $html = view(
            'customer/modules/sales/anoter',
            [
                'customer' => (int) $customerId
            ]
        );

        return $this->finish(
            true,
            'HISTORIAL DE ANOTACIONES CARGADO CORRECTAMENTE',
            $html
        );
    }
    public function anoter_datatable()
    {
        $customerId = $this->request->getPost('customer');
        $draw = (int) ($this->request->getPost('draw') ?? 0);

        if (
            empty($customerId) ||
            !is_numeric($customerId) ||
            (int) $customerId <= 0
        ) {
            return $this->response->setJSON([
                'status' => false,
                'msg' => 'NO SE RECIBIÓ UN CLIENTE VÁLIDO',
                'draw' => $draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ]);
        }

        try {
            $model = new CustomersCreditsDetailsModel();

            $result = $model->getAnoterDatatable(
                (int) $customerId,
                $this->request->getPost()
            );

            $result['status'] = true;

            return $this->response->setJSON($result);

        } catch (\Throwable $e) {
            log_message(
                'error',
                'ERROR DATATABLE ANOTACIONES DE CRÉDITO: '
                . $e->getMessage()
                . ' | Archivo: ' . $e->getFile()
                . ' | Línea: ' . $e->getLine()
            );

            return $this->response->setJSON([
                'status' => false,
                'msg' => 'ERROR AL CONSULTAR LAS ANOTACIONES',
                'debug' => ENVIRONMENT === 'development'
                    ? $e->getMessage()
                    : null,
                'draw' => $draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ]);
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
