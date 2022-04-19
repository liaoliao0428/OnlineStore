<?php

namespace App\Http\Controllers\Product;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProductModel;
use App\Http\Traits\ToolTrait;

class ProductController extends Controller
{
    use ToolTrait;
    //建構子 middleware設定
    public function __construct()
    {
        $this->middleware('authCheck');
    }
    
    /*********************************************view************************************************ */
    //首頁畫面
    public function index()
    {  
        return view('backstage.product.index'); //有帳號無token 代表帳號正卻，產生新token 存入資料庫跟session
    }
    /*********************************************view************************************************ */

}
