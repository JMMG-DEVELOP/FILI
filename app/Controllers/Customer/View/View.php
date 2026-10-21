<?php
namespace App\Controllers\Customer\View;
use App\Controllers\BaseController;
use App\Models\Customer\CustomerModel;
use App\Models\Customer\CustomersCreditsModel;
use App\Models\Customer\CustomersCreditsDetailsModel;
use App\Models\Customer\CustomerPaymentsModel;
// use App\Models\Auth\StatusModel;
class View extends BaseController
{
    protected $Model;
    protected $Status;

    public function __construct()
    {
        $this->Model = new CustomerModel();
    }
    public function view_panel_load()
    {
        $id = $this->request->getPost('id');
        $data = [
            'values' => $this->Model->get_ById($id),
        ];

        $html = view(
            'customer/panels/view',
            $data
        );

        return $this->finish(
            true,
            'PANEL DE CLIENTES CARGADO CORRECTAMENTE',
            $html
        );
    }

    public function home_view_load()
    {
        $id = $this->request->getPost('id');
        $Model = new CustomersCreditsModel();
        $data = [
            'values' => $Model->get_ByCustomer($id),
        ];

        $html = view(
            'customer/modules/view/home',
            $data
        );

        return $this->finish(
            true,
            'PANEL CARGADO CORRECTAMENTE',
            $html
        );
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
        $customer = $this->request->getPost('customer');

        if (
            empty($customer) ||
            !is_numeric($customer) ||
            (int) $customer <= 0
        ) {
            return $this->finish(
                false,
                'NO SE RECIBIÓ UN ID DE CLIENTE VÁLIDO'
            );
        }

        $Model = new CustomersCreditsModel();

        // Buscar la cuenta de crédito del cliente.
        $values = $Model
            ->where('customer', (int) $customer)
            ->first();

        // Si todavía no existe, preparar una cuenta nueva.
        if (!$values) {
            $values = [
                'id' => '',
                'customer' => (int) $customer,
                'amount' => 0
            ];
        }

        $html = view('customer/modules/view/form', [
            'values' => $values,
            'type' => 'add'
        ]);

        return $this->finish(
            true,
            'FORMULARIO DE CRÉDITO CARGADO CORRECTAMENTE',
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
            !is_numeric($customer) ||
            (int) $customer <= 0
        ) {
            return $this->finish(
                false,
                'EL ID DEL CLIENTE NO ES VÁLIDO'
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

            // Buscar y bloquear la cuenta existente.
            $credit = $db->query(
                'SELECT id, customer, amount
             FROM customers_credits
             WHERE customer = ?
             LIMIT 1
             FOR UPDATE',
                [(int) $customer]
            )->getRowArray();

            if ($credit) {
                // Ya existe una cuenta: sumar el nuevo monto.
                $creditId = (int) $credit['id'];
                $newBalance = (float) $credit['amount'] + $amount;

                $updated = $creditModel->update(
                    $creditId,
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
            } else {
                // No existe una cuenta: crear la primera.
                $creditId = $creditModel->insert([
                    'customer' => (int) $customer,
                    'amount' => $amount
                ], true);

                if (!$creditId) {
                    $db->transRollback();

                    return $this->finish(
                        false,
                        'NO SE PUDO CREAR LA CUENTA DE CRÉDITO'
                    );
                }

                $creditId = (int) $creditId;
                $newBalance = $amount;
            }

            // Registrar el movimiento de crédito.
            $detailId = $detailsModel->add_CustomersCreditsDetailsModel([
                'credit' => $creditId,
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
                    'id' => $creditId,
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
                'ERROR AL AGREGAR MONTO AL CRÉDITO: '
                . $e->getMessage()
                . ' | Archivo: ' . $e->getFile()
                . ' | Línea: ' . $e->getLine()
            );

            return $this->finish(
                false,
                'OCURRIÓ UN ERROR AL AGREGAR EL MONTO'
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
