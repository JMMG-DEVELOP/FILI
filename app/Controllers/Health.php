<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Health extends Controller
{
    public function index()
    {
        return $this->response
            ->setStatusCode(200)
            ->setContentType('application/json')
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->setJSON([
                'status' => 'ok',
                'server' => true,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
    }
}