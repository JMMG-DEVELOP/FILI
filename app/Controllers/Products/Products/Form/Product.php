<?php
namespace App\Controllers\Products\Products\Form;

use App\Controllers\BaseController;
use App\Models\Products\Brand\BrandsModel;
use App\Models\Products\Products\IvaModel;
use App\Models\Products\Products\PercentModel;
use App\Models\Products\Products\ProductSales;
use App\Models\Products\Section\SectionModel;
use App\Models\Products\Products\ProductModel;
use App\Models\Products\Products\StockModel;
use App\Services\ProductService;

class Product extends BaseController
{
  protected $BrandsModel;
  protected $IvaModel;
  protected $PercentModel;
  protected $SectionModel;
  protected $ProductSales;
  protected $ProductModel;

  public function __construct()
  {
    $this->BrandsModel = new BrandsModel();
    $this->IvaModel = new IvaModel();
    $this->PercentModel = new PercentModel();
    $this->SectionModel = new SectionModel();
    $this->ProductSales = new ProductSales();
    $this->ProductModel = new ProductModel();

  }

  public function open()
  {
    $values = $this->request->getPost();

    $type = $values['type'] ?? 'new';
    $code = trim($values['code'] ?? '');

    // ============================================================
    // VALIDAR TIPO DE OPERACIÓN
    // ============================================================

    if (!in_array($type, ['new', 'edit'], true)) {
      return $this->finish(
        false,
        'TIPO DE OPERACIÓN NO VÁLIDO'
      );
    }

    // ============================================================
    // PRODUCTO
    // ============================================================

    $product = null;

    if ($type === 'edit') {

      if ($code === '') {
        return $this->finish(
          false,
          'NO SE RECIBIÓ EL CÓDIGO DEL PRODUCTO'
        );
      }

      // Consultar todos los datos del producto
      $product = $this->ProductModel->getByCode($code);

      if (!$product) {
        return $this->finish(
          false,
          'NO SE ENCONTRÓ EL PRODUCTO'
        );
      }

    }
    // ============================================================
// SUCURSALES
// ============================================================

    $db = \Config\Database::connect();

    $sucursals = $db->table('sucursals')
      ->orderBy('id', 'ASC')
      ->get()
      ->getResultArray();


    // ============================================================
// STOCK POR SUCURSAL
// ============================================================

    $stockBySucursal = [];

    // Inicializar todas las sucursales con stock 0
    foreach ($sucursals as $sucursal) {

      $stockBySucursal[$sucursal['id']] = 0;

    }


    // ============================================================
// SI ES EDICIÓN, CONSULTAR STOCK DEL PRODUCTO
// ============================================================

    if ($type === 'edit') {

      $StockModel = new StockModel();

      $stocks = $StockModel
        ->where('product', $code)
        ->findAll();

      foreach ($stocks as $row) {

        $sucursalId = (int) $row['sucursal'];

        $stockBySucursal[$sucursalId] = $row['stock'];

      }

    }
    // =======================================================
    // DATOS PARA LA VISTA
    // =======================================================
    $data = [

      'title' => $values['title'] ?? '',
      'type' => $type,
      'page' => $values['page'] ?? '',

      'code' => $code,
      'product' => $product,

      'sections' => $this->SectionModel->findAll(),
      'brands' => $this->BrandsModel->findAll(),
      'sales' => $this->ProductSales->findAll(),
      'ivas' => $this->IvaModel->findAll(),

      'sucursals' => $sucursals,
      'stock' => $stockBySucursal,

    ];

    // =================================================
// SELECCIONAR VISTA SEGÚN OPERACIÓN
// ========================================================

    $view = $type === 'edit'
      ? 'products/products/form/update'
      : 'products/products/form/product';

    // ============================================================
// GENERAR HTML
// ============================================================

    $html = view($view, $data);

    return $this->finish(
      true,
      'CARGADO CORRECTAMENTE',
      $html
    );
  }

  public function confirm_open()
  {
    $values = $this->request->getPost();
    $data = [

    ];
    $html = view(
      'products/products/form/confirm',
      $data
    );
  }
  public function code_validation()
  {
    $productService = new ProductService();
    $result = $productService->validate_code(
      $this->request->getPost('code')
    );

    return $this->response->setJSON(
      array_merge($result, [
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash()
      ])
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