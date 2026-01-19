<?php
namespace App\Controllers\Products\Products;

use App\Controllers\BaseController;
use App\Libraries\Infopage;

use App\Models\Products\ProductsModel;


class Access extends BaseController
{
    public function index()
    {
        if (session()->get('logged')) {

            $infopage = new Infopage();

            $info = [
                'title' => 'Products',
            ];
            $page = $infopage->infopage($info);
            return view('Products/index', $page);
            // return view('products/index', [ ]);
        }
    }

}
