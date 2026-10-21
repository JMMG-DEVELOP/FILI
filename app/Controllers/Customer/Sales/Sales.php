<?php
namespace App\Controllers\Customer\Sales;
use App\Controllers\BaseController;
// use App\Models\Customer\CustomerModel;
use App\Models\Customer\CustomersCreditsModel;
// use App\Models\Customer\CustomersCreditsDetailsModel;
// use App\Models\Customer\CustomerPaymentsModel;
// use App\Models\Auth\StatusModel;
use App\Models\Box\SalesModel;
class Sales extends BaseController
{
    protected $Model;
    protected $Status;

    public function __construct()
    {
        $this->Model = new SalesModel();
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

        $values = $this->Model->getSales([
            'customer' => (int) $id,
        ]);
        $html = view(
            'customer/modules/sales/home',
            [
                'customer_id' => (int) $id,
            ]
        );

        return $this->finish(
            true,
            'VENTAS CARGADAS CORRECTAMENTE',
            $html
        );
    }
    public function sales_datatable()
    {
        $draw = (int) ($this->request->getPost('draw') ?? 0);

        $start = max(
            0,
            (int) ($this->request->getPost('start') ?? 0)
        );

        $length = (int) ($this->request->getPost('length') ?? 10);

        $searchData = $this->request->getPost('search');

        $search = is_array($searchData)
            ? trim((string) ($searchData['value'] ?? ''))
            : '';

        $customerId = $this->request->getPost('customer');

        if (
            empty($customerId) ||
            !is_numeric($customerId)
        ) {
            return $this->response->setJSON([
                'status' => false,
                'msg' => 'NO SE RECIBIÓ UN CLIENTE VÁLIDO',
                'draw' => $draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
            ]);
        }

        try {
            $sales = $this->Model->getSales([
                'customer' => (int) $customerId,
            ]);

            $data = [];

            foreach ($sales as $sale) {
                $data[] = [
                    'id' => $sale['id'] ?? '',

                    'number' => $sale['number'] ?? '',

                    'type_name' => $sale['sales_type_name'] ?? '',

                    'invoice_type_name' => $sale['invoice_type_name'] ?? '',

                    'date_time' => trim(
                        ($sale['date'] ?? '') . ' ' .
                        ($sale['time'] ?? '')
                    ),

                    'total' => $sale['total']
                        ?? $sale['total_price']
                        ?? $sale['mount']
                        ?? 0,

                    'payment_name' => $sale['payment_name']
                        ?? 'SIN PAGO',

                    'sucursal_name' => $sale['sucursal_name'] ?? '',

                    'margin' => $sale['total_margin'] ?? 0,

                    'user_name' => $sale['user_name'] ?? '',
                ];
            }

            $recordsTotal = count($data);

            /*
             * Búsqueda global.
             */
            if ($search !== '') {
                $data = array_values(
                    array_filter(
                        $data,
                        function ($row) use ($search) {
                            foreach ($row as $value) {
                                if (
                                    stripos(
                                        (string) $value,
                                        $search
                                    ) !== false
                                ) {
                                    return true;
                                }
                            }

                            return false;
                        }
                    )
                );
            }

            $recordsFiltered = count($data);

            /*
             * Ordenamiento.
             */
            $orderData = $this->request->getPost('order');
            $columnsData = $this->request->getPost('columns');

            $allowedColumns = [
                'id',
                'number',
                'type_name',
                'invoice_type_name',
                'date_time',
                'total',
                'payment_name',
                'sucursal_name',
                'margin',
                'user_name',
            ];

            if (
                is_array($orderData) &&
                isset($orderData[0]['column']) &&
                is_array($columnsData)
            ) {
                $columnIndex = (int) $orderData[0]['column'];

                $columnName = $columnsData[$columnIndex]['data']
                    ?? 'id';

                $direction = strtolower(
                    $orderData[0]['dir'] ?? 'desc'
                ) === 'asc' ? 'asc' : 'desc';

                if (in_array($columnName, $allowedColumns, true)) {
                    usort(
                        $data,
                        function ($a, $b) use ($columnName, $direction) {
                            $valueA = $a[$columnName] ?? '';
                            $valueB = $b[$columnName] ?? '';

                            if (
                                is_numeric($valueA) &&
                                is_numeric($valueB)
                            ) {
                                $result = (float) $valueA
                                    <=> (float) $valueB;
                            } else {
                                $result = strnatcasecmp(
                                    (string) $valueA,
                                    (string) $valueB
                                );
                            }

                            return $direction === 'des'
                                ? $result
                                : -$result;
                        }
                    );
                }
            }

            /*
             * Paginación.
             */
            if ($length !== -1) {
                $data = array_slice(
                    $data,
                    $start,
                    max(1, $length)
                );
            } else {
                $data = array_slice($data, $start);
            }

            return $this->response->setJSON([
                'status' => true,
                'draw' => $draw,
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            log_message(
                'error',
                'ERROR DATATABLE VENTAS CLIENTE: ' .
                $e->getMessage()
            );

            return $this->response->setJSON([
                'status' => false,
                'msg' => 'ERROR AL CONSULTAR LAS VENTAS',
                'draw' => $draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
            ]);
        }
    }

    public function credit_payment_form_open()
    {
        $id = $this->request->getPost('id');

        if (empty($id) || !is_numeric($id)) {
            return $this->finish(
                false,
                'NO SE RECIBIÓ UN ID DE CRÉDITO VÁLIDO'
            );
        }

        $Model = new CustomersCreditsModel();

        $values = $Model->get_ById($id);

        if (!$values) {
            return $this->finish(
                false,
                'NO SE ENCONTRÓ EL REGISTRO DE CRÉDITO'
            );
        }

        $html = view('customer/modules/view/form', [
            'values' => $values,
            'type' => 'payment'
        ]);

        return $this->finish(
            true,
            'FORMULARIO DE PAGO CARGADO CORRECTAMENTE',
            $html
        );
    }

    public function credit_payment_form_save()
    {
        $post = $this->request->getPost();
        $values = $post['values'] ?? [];

        if (!is_array($values)) {
            return $this->finish(
                false,
                'LOS DATOS RECIBIDOS NO SON VÁLIDOS'
            );
        }

        $id = $values['id'] ?? null;
        $customer = $values['customer'] ?? null;
        $amountInput = (string) ($values['amount'] ?? '');

        // Convierte montos como 50.000 a 50000.
        $amount = (float) preg_replace('/[^\d]/', '', $amountInput);

        if (
            !is_numeric($id) ||
            (int) $id <= 0 ||
            !is_numeric($customer) ||
            (int) $customer <= 0
        ) {
            return $this->finish(
                false,
                'EL ID DEL CRÉDITO O DEL CLIENTE NO ES VÁLIDO'
            );
        }

        if ($amount <= 0) {
            return $this->finish(
                false,
                'EL MONTO DEL PAGO DEBE SER MAYOR A CERO'
            );
        }

        $db = \Config\Database::connect();

        $creditModel = new CustomersCreditsModel();
        $paymentModel = new CustomerPaymentsModel();

        $db->transBegin();

        try {
            // Bloquea el crédito mientras se procesa el pago.
            $credit = $db->query(
                'SELECT id, customer, amount
             FROM customers_credits
             WHERE id = ? AND customer = ?
             FOR UPDATE',
                [(int) $id, (int) $customer]
            )->getRowArray();

            if (!$credit) {
                $db->transRollback();

                return $this->finish(
                    false,
                    'NO SE ENCONTRÓ EL CRÉDITO PARA ESTE CLIENTE'
                );
            }

            $balance = (float) $credit['amount'];

            if ($balance <= 0) {
                $db->transRollback();

                return $this->finish(
                    false,
                    'EL CLIENTE NO TIENE SALDO PENDIENTE'
                );
            }

            // El pago aplicado nunca supera la deuda.
            $paymentAmount = min($amount, $balance);

            // El excedente se devuelve como vuelto.
            $change = max(0, $amount - $balance);

            $newBalance = max(0, $balance - $paymentAmount);

            // Actualizar el saldo pendiente.
            $updated = $creditModel->update(
                (int) $id,
                ['amount' => $newBalance]
            );

            if ($updated === false) {
                $db->transRollback();

                return $this->finish(
                    false,
                    'NO SE PUDO ACTUALIZAR EL SALDO DEL CRÉDITO'
                );
            }

            // Registrar el pago en customer_payments.
            $paymentId = $paymentModel->add_payment([
                'customer_credit' => (int) $id,
                'date' => date('Y-m-d'),
                'time' => date('H:i:s'),
                'amount' => $paymentAmount,
                'user' => (int) session('id'),
                'detail' => trim((string) ($values['detail'] ?? '')),

            ]);

            if (!$paymentId) {
                $db->transRollback();

                return $this->finish(
                    false,
                    'NO SE PUDO REGISTRAR EL PAGO'
                );
            }

            if ($db->transStatus() === false) {
                $db->transRollback();

                return $this->finish(
                    false,
                    'ERROR AL PROCESAR EL PAGO'
                );
            }

            $db->transCommit();

            return $this->finish(
                true,
                'PAGO REGISTRADO CORRECTAMENTE',
                '',
                [
                    'id' => (int) $id,
                    'customer' => (int) $customer,
                    'payment_id' => (int) $paymentId,
                    'payment' => $paymentAmount,
                    'change' => $change,
                    'new_balance' => $newBalance
                ]
            );

        } catch (\Throwable $e) {
            $db->transRollback();

            log_message(
                'error',
                'ERROR AL REGISTRAR PAGO DE CRÉDITO: ' . $e->getMessage()
            );

            return $this->finish(
                false,
                'OCURRIÓ UN ERROR AL REGISTRAR EL PAGO'
            );
        }
    }

    public function credit_add_form_open()
    {

        $id = $this->request->getPost('id');

        if (empty($id) || !is_numeric($id)) {
            return $this->finish(
                false,
                'NO SE RECIBIÓ UN ID DE CRÉDITO VÁLIDO'
            );
        }

        $Model = new CustomersCreditsModel();

        $values = $Model->get_ById($id);

        if (!$values) {
            return $this->finish(
                false,
                'NO SE ENCONTRÓ EL REGISTRO DE CRÉDITO'
            );
        }

        $html = view('customer/modules/view/form', [
            'values' => $values,
            'type' => 'add'
        ]);

        return $this->finish(
            true,
            'FORMULARIO DE PAGO CARGADO CORRECTAMENTE',
            $html
        );
    }

    public function credit_add_form_save()
    {
        $post = $this->request->getPost();
        $values = $post['values'] ?? [];

        if (!is_array($values)) {
            return $this->finish(
                false,
                'LOS DATOS RECIBIDOS NO SON VÁLIDOS'
            );
        }

        $id = $values['id'] ?? null;
        $customer = $values['customer'] ?? null;
        $amountInput = (string) ($values['amount'] ?? '');

        // Convierte montos como 50.000 a 50000.
        $amount = (float) preg_replace('/[^\d]/', '', $amountInput);

        if (
            !is_numeric($id) ||
            (int) $id <= 0 ||
            !is_numeric($customer) ||
            (int) $customer <= 0
        ) {
            return $this->finish(
                false,
                'EL ID DEL CRÉDITO O DEL CLIENTE NO ES VÁLIDO'
            );
        }

        if ($amount <= 0) {
            return $this->finish(
                false,
                'EL MONTO A AGREGAR DEBE SER MAYOR A CERO'
            );
        }

        $db = \Config\Database::connect();

        $creditModel = new CustomersCreditsModel();
        $detailsModel = new CustomersCreditsDetailsModel();

        try {
            $db->transBegin();

            // Consultar y bloquear el crédito durante la operación.
            $credit = $db->query(
                'SELECT id, customer, amount
             FROM customers_credits
             WHERE id = ? AND customer = ?
             FOR UPDATE',
                [(int) $id, (int) $customer]
            )->getRowArray();

            if (!$credit) {
                $db->transRollback();

                return $this->finish(
                    false,
                    'NO SE ENCONTRÓ EL CRÉDITO PARA ESTE CLIENTE'
                );
            }

            $balance = (float) $credit['amount'];

            // Sumar el monto al saldo actual.
            $newBalance = $balance + $amount;

            $updated = $creditModel->update(
                (int) $id,
                [
                    'amount' => $newBalance
                ]
            );

            if ($updated === false) {
                $db->transRollback();

                return $this->finish(
                    false,
                    'NO SE PUDO ACTUALIZAR EL SALDO DEL CRÉDITO'
                );
            }

            // Registrar el movimiento de crédito.
            // Se asume que credit_type = 2 corresponde a agregar crédito.
            $detailId = $detailsModel->add_CustomersCreditsDetailsModel([
                'credit' => (int) $id,
                'credit_type' => 3,
                'mount' => (string) $amount,
                'date' => date('Y-m-d'),
                'time' => date('H:i:s'),
                'user' => session()->get('id'),
            ]);

            if (!$detailId) {
                $db->transRollback();

                return $this->finish(
                    false,
                    'NO SE PUDO REGISTRAR EL MOVIMIENTO DEL CRÉDITO'
                );
            }

            if ($db->transStatus() === false) {
                $db->transRollback();

                return $this->finish(
                    false,
                    'ERROR AL PROCESAR EL MOVIMIENTO DEL CRÉDITO'
                );
            }

            $db->transCommit();

            return $this->finish(
                true,
                'MONTO AÑADIDO EXITOSAMENTE',
                '',
                [
                    'id' => (int) $id,
                    'customer' => (int) $customer,
                    'detail_id' => (int) $detailId,
                    'amount_added' => $amount,
                    'new_balance' => $newBalance
                ]
            );

        } catch (\Throwable $e) {
            $db->transRollback();

            log_message(
                'error',
                'ERROR AL AGREGAR MONTO AL CRÉDITO: ' . $e->getMessage()
            );

            return $this->finish(
                false,
                'OCURRIÓ UN ERROR AL AGREGAR EL MONTO'
            );
        }
    }


    public function sales_card()
    {
        $customerId = $this->request->getPost('customer');

        if (empty($customerId) || !is_numeric($customerId)) {
            return $this->response->setJSON([
                'status' => false,
                'msg' => 'NO SE RECIBIÓ UN CLIENTE VÁLIDO',
            ]);
        }

        try {
            $model = new CustomersCreditsModel();

            $result = $model->getSalesCard((int) $customerId);

            return $this->response->setJSON([
                'status' => true,
                'data' => [
                    'total_sales' => (float) ($result['total_sales'] ?? 0),
                    'total_cost' => (float) ($result['total_cost'] ?? 0),
                    'total_margin' => (float) ($result['total_margin'] ?? 0),
                    'total_purchases' => (int) ($result['total_purchases'] ?? 0),
                    'total_credit' => (float) ($result['total_credit'] ?? 0),
                    'total_credit_purchases' => (int) ($result['total_credit_purchases'] ?? 0),
                ],
            ]);

        } catch (\Throwable $e) {
            log_message(
                'error',
                'ERROR AL CONSULTAR TARJETAS DE VENTAS DEL CLIENTE: ' . $e->getMessage()
            );

            return $this->response->setJSON([
                'status' => false,
                'msg' => 'ERROR AL CONSULTAR LOS TOTALES DEL CLIENTE',
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
