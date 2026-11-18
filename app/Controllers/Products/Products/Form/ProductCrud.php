<?php
namespace App\Controllers\Products\Products\Form;

use App\Controllers\BaseController;
use App\Models\Products\Products\ProductModel;

use App\Services\ProductService;

class ProductCrud extends BaseController
{
  protected $ProductModel;

  public function __construct()
  {

    $this->ProductModel = new ProductModel();

  }
  public function create()
  {
    try {

      // ============================================================
      // 1. PERMISOS
      // ============================================================

      if (!can('product_product_add')) {
        return $this->finish(
          false,
          'No tienes permiso para registrar productos'
        );
      }

      // ============================================================
      // 2. RECIBIR DATOS
      // ============================================================

      $post = $this->request->getPost();

      $code = trim($post['code'] ?? '');
      $description = trim($post['description'] ?? '');

      if ($code === '' || $description === '') {
        return $this->finish(
          false,
          'El código y la descripción son obligatorios'
        );
      }

      // ============================================================
      // 3. VALIDAR CÓDIGO
      // ============================================================

      if ($this->ProductModel->getByCode($code)) {
        return $this->finish(
          false,
          'El código ya se encuentra registrado'
        );
      }

      // ============================================================
      // 4. VALIDAR TIPO DE VENTA
      // ============================================================

      $sales = (int) ($post['sales'] ?? 0);

      if (!in_array($sales, [1, 2, 3, 4], true)) {
        return $this->finish(
          false,
          'Tipo de venta inválido'
        );
      }

      // ============================================================
      // 5. PREPARAR INFORMACIÓN
      // ============================================================

      $formatter = new \App\Libraries\ProductsFormatter();

      $data = $formatter->tables_formatters($post);

      // ============================================================
      // 6. CONTROLAR COSTO SEGÚN PERMISO
      // ============================================================

      if (!can('product_cost_view')) {
        $data['costs']['cost'] = 0;
      }

      // ============================================================
      // 7. GUARDAR PRODUCTO
      // ============================================================

      $service = new ProductService();

      $result = $service->add($data);

      if (!$result['status']) {
        return $this->finish(
          false,
          $result['message']
        );
      }

      // ============================================================
      // 8. RESPUESTA
      // ============================================================

      return $this->finish(
        true,
        'PRODUCTO REGISTRADO CORRECTAMENTE',
        '',
        [
          'code' => $code
        ]
      );

    } catch (\Throwable $e) {

      log_message(
        'error',
        'PRODUCT CREATE ERROR: ' . $e->getMessage()
        . ' | Archivo: ' . $e->getFile()
        . ' | Línea: ' . $e->getLine()
        . ' | Trace: ' . $e->getTraceAsString()
      );

      return $this->finish(
        false,
        'Ocurrió un error al registrar el producto'
      );
    }

  }

  public function update()
  {
    try {

      // ==========================================
      // 1. PERMISO
      // ==========================================

      if (!can('product_product_edit')) {
        return $this->finish(
          false,
          'No tienes permiso para editar productos'
        );
      }

      // ==========================================
      // 2. DATOS
      // ==========================================

      $post = $this->request->getPost();

      $code = trim($post['code'] ?? '');
      $description = trim($post['description'] ?? '');

      if ($code === '' || $description === '') {
        return $this->finish(
          false,
          'El código y la descripción son obligatorios'
        );
      }

      // ==========================================
      // 3. VERIFICAR PRODUCTO
      // ==========================================

      $product = $this->ProductModel->getByCode($code);

      if (!$product) {
        return $this->finish(
          false,
          'El producto que intentas editar no existe'
        );
      }

      // ==========================================
      // 4. FORMATEAR DATOS
      // ==========================================

      $formatter = new \App\Libraries\ProductsFormatter();

      $data = $formatter->tables_formatters($post);

      // Si no tiene permiso para ver costos,
      // no modificar el costo existente.
      if (!can('product_cost_view')) {
        unset($data['costs']);
      }

      // ==========================================
      // 5. ACTUALIZAR
      // ==========================================

      $service = new ProductService();

      $userId = (int) session('id');

      if ($userId <= 0) {
        return $this->finish(
          false,
          'NO SE PUDO IDENTIFICAR AL USUARIO'
        );
      }

      $service = new ProductService();

      $service->edit($data, $userId);

      return $this->finish(
        true,
        'PRODUCTO ACTUALIZADO CORRECTAMENTE',
        '',
        ['code' => $code]
      );

    } catch (\Throwable $e) {

      log_message(
        'error',
        'PRODUCT UPDATE ERROR: ' .
        $e->getMessage() .
        ' | Archivo: ' . $e->getFile() .
        ' | Línea: ' . $e->getLine()
      );

      return $this->finish(
        false,
        'Ocurrió un error al actualizar el producto'
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