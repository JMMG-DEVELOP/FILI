<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AjaxFilter implements FilterInterface
{
  public function before(RequestInterface $request, $arguments = null)
  {
    if (!$request->isAJAX()) {

      return service('response')->setJSON([
        'status' => false,
        'message' => 'Invalid request',
        'csrf' => csrf_hash()
      ])->setStatusCode(403);
    }
  }

  public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
  {
    //
  }
}