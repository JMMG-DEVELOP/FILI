<?php
namespace App\Controllers\Products\Products\Shopp;
use App\Controllers\BaseController;
use App\Models\Products\Products\ProductModel;
use App\Models\Products\Products\ShoppModel;

use App\Models\Provider\ProviderModel;
use App\Models\Point\SucursalsModel;
use App\Services\StockService;

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
        $code = trim($this->request->getPost('code') ?? '');

        if ($code === '') {
            return $this->finish(
                false,
                'DEBE INGRESAR UN CÓDIGO',
                '',
                []
            );
        }

        $ProductModel = new ProductModel();

        // Buscar producto
        $product = $ProductModel->getByCode($code);

        if (!$product) {
            return $this->finish(
                false,
                'PRODUCTO NO ENCONTRADO',
                '',
                [
                    'product' => null,
                    'lastPurchase' => null
                ]
            );
        }

        // Buscar última compra del producto
        $lastPurchase = $ProductModel->getLastPurchaseHistory($code);

        return $this->finish(
            true,
            'PRODUCTO ENCONTRADO',
            '',
            [
                'product' => $product,
                'lastPurchase' => $lastPurchase
            ]
        );
    }

    public function update_stock()
    {
        // =====================================================
        // 1. VALIDAR PERMISOS
        // =====================================================

        if (!can('product_stock_edit')) {
            return $this->finish(
                false,
                'NO TIENES PERMISOS PARA MODIFICAR EL STOCK',
                '',
                []
            );
        }

        // =====================================================
        // 2. RECIBIR DATOS
        // =====================================================

        $code = $this->request->getPost('code');

        $sucursal = (int) $this->request->getPost('sucursal');

        $adjustment = $this->request->getPost('adjustment');

        // =====================================================
        // 3. VALIDAR DATOS
        // =====================================================

        if (
            empty($code) ||
            $sucursal <= 0 ||
            !is_numeric($adjustment)
        ) {
            return $this->finish(
                false,
                'DATOS INCORRECTOS',
                '',
                []
            );
        }

        $adjustment = (float) $adjustment;

        if ($adjustment == 0) {
            return $this->finish(
                false,
                'INGRESA UN AJUSTE DIFERENTE DE CERO',
                '',
                []
            );
        }

        // =====================================================
        // 4. ACTUALIZAR STOCK Y REGISTRAR HISTORIAL
        // =====================================================

        $productModel = new ProductModel();

        $result = $productModel->adjustStock(
            $code,
            $sucursal,
            $adjustment,
            (int) session('id'),
            3 // Corrección
        );

        // =====================================================
        // 5. VERIFICAR RESULTADO
        // =====================================================

        if (!$result['success']) {
            return $this->finish(
                false,
                $result['message'],
                '',
                $result
            );
        }

        // =====================================================
        // 6. RESPUESTA
        // =====================================================

        return $this->finish(
            true,
            $result['message'],
            '',
            [
                'sucursal' => $sucursal,
                'stock' => $result['stock'],
                'movement_id' => $result['movement_id']
            ]
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


    public function save()
    {
        if (!can('product_product_edit')) {
            return $this->finish(
                false,
                'NO TIENES PERMISOS PARA REGISTRAR LA COMPRA',
                '',
                []
            );
        }

        try {

            // ==========================================
            // 1. RECIBIR DATOS
            // ==========================================

            $data = [
                'product' => trim((string) $this->request->getPost('shopp_code')),

                'price' => (float) $this->request->getPost('price_shop'),

                'quantity' => (float) $this->request->getPost('quantity_shop'),

                'provider' => (int) $this->request->getPost('provider'),

                'sucursal' => (int) $this->request->getPost('sucursal'),

                'user' => (int) session('id'),

                'cost' => (float) $this->request->getPost('cost'),

                'price_one' => (float) $this->request->getPost('shopp_price_one'),

                'price_two' => (float) $this->request->getPost('shopp_price_two'),

                'price_three' => (float) $this->request->getPost('shopp_price_three'),

                'cant_two' => (int) $this->request->getPost('shopp_cant_two'),

                'cant_three' => (int) $this->request->getPost('shopp_cant_three')
            ];

            // ==========================================
            // 2. VALIDACIONES
            // ==========================================

            if ($data['product'] === '') {
                throw new \RuntimeException(
                    'DEBES SELECCIONAR UN PRODUCTO'
                );
            }

            if ($data['price'] <= 0) {
                throw new \RuntimeException(
                    'EL PRECIO DE COMPRA NO ES VÁLIDO'
                );
            }

            if ($data['quantity'] <= 0) {
                throw new \RuntimeException(
                    'LA CANTIDAD NO ES VÁLIDA'
                );
            }

            if ($data['provider'] <= 0) {
                throw new \RuntimeException(
                    'DEBES SELECCIONAR UN PROVEEDOR'
                );
            }

            if ($data['sucursal'] <= 0) {
                throw new \RuntimeException(
                    'DEBES SELECCIONAR UNA SUCURSAL'
                );
            }

            if ($data['user'] <= 0) {
                throw new \RuntimeException(
                    'NO SE PUDO IDENTIFICAR AL USUARIO'
                );
            }

            if ($data['cost'] < 0) {
                throw new \RuntimeException(
                    'EL COSTO NO ES VÁLIDO'
                );
            }

            foreach ([
                'price_one',
                'price_two',
                'price_three',
                'cant_two',
                'cant_three'
            ] as $field) {

                if ($data[$field] < 0) {
                    throw new \RuntimeException(
                        'LOS PRECIOS O CANTIDADES NO SON VÁLIDOS'
                    );
                }
            }

            // ==========================================
            // 3. GUARDAR COMPRA
            // ==========================================

            $model = new ShoppModel();

            // CORREGIDO: un solo signo $
            $result = $model->savePurchase($data);

            if (!$result['status']) {
                return $this->finish(
                    false,
                    $result['message'],
                    '',
                    []
                );
            }

            // ==========================================
            // 4. RESPUESTA
            // ==========================================

            return $this->finish(
                true,
                $result['message'],
                '',
                [
                    'product' => $data['product'],
                    'sucursal' => $data['sucursal'],
                    'quantity' => $data['quantity'],
                    'stock' => $result['stock']
                ]
            );

        } catch (\Throwable $e) {

            log_message(
                'error',
                'ERROR AL REGISTRAR COMPRA: ' . $e->getMessage()
            );

            log_message(
                'error',
                'ARCHIVO: ' . $e->getFile() .
                ' LINEA: ' . $e->getLine()
            );

            return $this->finish(
                false,
                $e instanceof \RuntimeException
                ? $e->getMessage()
                : 'ERROR AL REGISTRAR LA COMPRA',
                '',
                []
            );
        }
    }

    private function parseMoney($value): int
    {
        return (int) preg_replace(
            '/[^0-9]/',
            '',
            (string) $value
        );
    }

    private function parseDecimal($value): float
    {
        $value = trim((string) $value);
        $value = str_replace(',', '.', $value);
        $value = preg_replace('/[^0-9.]/', '', $value);

        return (float) $value;
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
