<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;


class HomeFilter implements FilterInterface
{
  public function before(RequestInterface $request, $arguments = null)
  {
    service('home');
    if (session()->get('logged')) {
      return redirect()->to(base_url('dashboard'));
    }
  }

  public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
  {
    // Do something here
  }
}
