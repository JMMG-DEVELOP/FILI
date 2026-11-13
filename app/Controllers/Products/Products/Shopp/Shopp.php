<?php
namespace App\Controllers\Products\Products\Shopp;
use App\Controllers\BaseController;
use App\Models\Products\Products\ProductModel;
use App\Models\Provider\ProviderModel;
use App\Models\Point\SucursalsModel;
use App\Models\Products\Products\StockModel;

class Shopp extends BaseController
{

    public function panel_load()
    {
        $ProviderModel = new ProviderModel();
        $SucursalModel = new SucursalsModel();
        $data = [
            'provider' => $ProviderModel->findAll(),
            'sucursals' => $SucursalModel->findAll(),
        ];


        $html = view('products/products/shopp/shopp', $data);

        return $this->finish(
            true,
            'CARGADO CORRECTAMENTE',
            $html,

        );
    }

    public function shopp_win()
    {
        $ProviderModel = new ProviderModel();
        $SucursalModel = new SucursalsModel();
        $data = [
            'provider' => $ProviderModel->findAll(),
            'sucursal' => $SucursalModel->findAll(),
        ];

        $html = view('products/products/shopp/shopp_win', $data);

        return $this->finish(
            true,
            'CARGADO CORRECTAMENTE',
            $html,

        );
    }

    public function shopp_search()
    {

        if ($this->request->isAJAX()) {
            $productModel = new ProductModel();
            $value = $this->request->getPost('value');
            $result = [
                'result' => $productModel->getBySearch($value)
            ];
            $html = view('products/products/shopp/shopp_search_result', $result);

            return $this->response->setJSON([
                'status' => true,
                'msg' => $result,
                'html' => $html,
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash()
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
