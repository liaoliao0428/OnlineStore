<?php

namespace App\Http\Controllers\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\ProductModel;
use App\Http\Traits\ToolTrait;
use Illuminate\Support\Facades\Cookie;


class ProductController extends Controller
{
    use ToolTrait;
    //建構子 middleware設定
    public function __construct()
    {
        $this->middleware('authCheck');
    }
    
    /*********************************************view************************************************ */
    // 首頁畫面
    public function index($categoryId = 0)
    {  
        Cookie::queue('categoryId', $categoryId, 1800);
        return view('backstage.product.index'); 
    }

    // 新增商品畫面
    public function add($categoryId = 0)
    {  
        //資料存cookie
        Cookie::queue('categoryId', $categoryId, 1800);
        return view('backstage.product.add'); 
    }

    // 編輯商品畫面
    public function edit($categoryId = 0)
    {  
        Cookie::queue('categoryId', $categoryId, 1800);
        return view('backstage.product.edit'); 
    }
    /*********************************************view************************************************ */

    //取得分類商品
    public function product(Request $request)
    {
        $categoryId = $request->categoryId;
        $product = ProductModel::select_product_with_categoryId_db($categoryId);
        if(!empty($product)){
            return response()->json(['product' => $product], Response::HTTP_OK);
        }else{
            return response()->json(['product' => '無資料'], Response::HTTP_OK);
        }
        
    }

}
