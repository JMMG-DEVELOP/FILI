<?php
namespace App\Controllers\Customer\Home;
use App\Controllers\BaseController;
use App\Models\Customer\CustomerModel;
use App\Models\Customer\CustomerDatatablesModel;
use App\Models\Auth\StatusModel;
class Home extends BaseController
{
    protected $Model;
    protected $Status;

    public function __construct()
    {
        $this->Model = new CustomerModel();
        $this->Status = new StatusModel();
    }
    public function panel_load()
    {
        $data = [];

        $html = view(
            'customer/modules/home/home',
            $data
        );

        return $this->finish(
            true,
            'PANEL DE CLIENTES CARGADO CORRECTAMENTE',
            $html
        );
    }

    public function datatable()
    {
        $model = new CustomerDatatablesModel();

        $post = $this->request->getPost();

        $draw = (int) ($post['draw'] ?? 0);

        $recordsTotal = $model->countAll();

        $recordsFiltered = $model->countFiltered($post);

        $customers = $model->getRows($post);

        return $this->response->setJSON([
            'status' => true,
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $customers,
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash()
        ]);
    }

    public function form_new_open()
    {

        $data = [
            'title' => 'Nuevo Cliente',
            'type' => 'new',
            'values' => [],
            'status' => $this->Status->get_All()
        ];
        $html = view(
            'customer/modules/home/form',
            $data
        );
        return $this->finish(
            true,
            '',
            $html
        );
    }

    public function form_new_save()
    {
        // Verificar permiso.
        if (!can('customer_add')) {
            return $this->finish(
                false,
                'NO TIENES PERMISO PARA AGREGAR CLIENTES'
            );
        }

        $post = $this->request->getPost();

        // Los datos del formulario llegan dentro de "values".
        $values = $post['values'] ?? [];

        if (!is_array($values)) {
            return $this->finish(
                false,
                'LOS DATOS RECIBIDOS NO SON VÁLIDOS'
            );
        }

        // El usuario se obtiene de la sesión, nunca del formulario.
        $values['user'] = session()->get('id');
        $values['date'] = date('Y-m-d');
        $values['time'] = date('H:i:s');

        $model = new CustomerModel();

        $result = $model->create($values);

        if (!$result['status']) {
            return $this->finish(
                false,
                $result['msg'],
                '',
                [
                    'errors' => $result['errors'] ?? [],
                    'values' => $values
                ]
            );
        }

        return $this->finish(
            true,
            $result['msg'],
            '',
            [
                'id' => $result['id']
            ]
        );
    }

    public function form_edit_open()
    {
        $values = $this->request->getPost();

        $id = $values['id'] ?? null;

        if (!$id) {
            return $this->finish(
                false,
                'No se recibió el ID.',
                ''
            );
        }

        $values = $this->Model->get_ById($id);

        if (!$values) {
            return $this->finish(
                false,
                'No se encontró ID.',
                ''
            );
        }

        $data = [
            'type' => 'edit',
            'title' => 'Editar Cliente',
            'values' => $values,
            'status' => $this->Status->get_All()

        ];

        $html = view(
            'customer/modules/home/form',
            $data
        );

        return $this->finish(
            true,
            '',
            $html,
            $values
        );
    }

    public function form_edit_save()
    {

        $values = $this->request->getPost();

        $value = $values['values'] ?? [];
        $id = $values['id'] ?? null;

        if (!$id) {
            return $this->finish(
                false,
                'NO SE RECIBIO EL ID'
            );
        }

        if (!$this->Model->edit($value, $id)) {
            return $this->finish(
                false,
                'ERROR AL EDITAR',
                [
                    'error' => $this->Model->errors()
                        ? implode(', ', $this->Model->errors())
                        : 'ERROR AL EDITAR',
                    'values' => $values
                ]
            );
        }

        return $this->finish(
            true,
            'EDITADO CORRECTAMENTE'
        );
    }

    public function delete_save()
    {
        if (!can('customer_delete')) {
            return $this->finish(
                false,
                'NO TIENES PERMISO PARA ELIMINAR CLIENTES'
            );
        }

        $values = $this->request->getPost();
        $id = $values['id'] ?? null;

        if (empty($id) || !is_numeric($id)) {
            return $this->finish(
                false,
                'NO SE RECIBIÓ UN ID VÁLIDO'
            );
        }

        $result = $this->Model->quit($id);

        if (!$result['status']) {
            return $this->finish(
                false,
                $result['msg']
            );
        }

        return $this->finish(
            true,
            $result['msg']
        );
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
