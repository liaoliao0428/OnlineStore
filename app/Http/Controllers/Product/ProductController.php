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
    public function edit($productId = 0)
    {  
        return view('backstage.product.edit'); 
    }
    /*********************************************view************************************************ */

    //取得商品 有分類取分類 有指定取指定 沒指定拿全部
    public function product(Request $request)
    {
        $categoryId = $request->categoryId;
        $productId = $request->productId;

        // 撈指定分類商品
        if($categoryId == 0){   // categoryId != 0 撈全部
            $productsMain = ProductModel::select_product_db();
        }else{
            $productsMain = ProductModel::select_product_with_categoryId_db($categoryId);
        }

        // 如果有productId 就撈指定的
        if(!empty($productId)){
            $productsMain = ProductModel::select_product_with_productId_db($productId);
        }
        
        $product = $this->productArray($productsMain);

        // 回傳資料
        if(!empty($product)){
            return response()->json(['product' => $product], Response::HTTP_OK);
        }else{
            return response()->json(['product' => '無資料'], Response::HTTP_OK);
        }
    }

    // 商品陣列組合
    public function productArray($productsMain)
    {
        $product = array();
        foreach($productsMain as $productMain){
            $productId = $productMain->productId;
            // 取得商品細項
            $productDetail = ProductModel::select_product_detail_with_productId_db($productId);
            // 商品細項塞入商品主檔
            $productMain->productDetail = $productDetail;
            $product[] = $productMain;
        }
        return $product;
    }

    //商品新增
    public function insert(Request $request)
    {
        $product = $request->all();
        unset($product['_token']);
        $product['productId'] = $this->randomString(13);
        ProductModel::insert_product_db($product);
        return redirect()->route('productEdit',['productId'=>$product['productId']]);
    }

    //商品更新
    public function update(Request $request)
    {
        $product = $request->all();
        $productId = $request->productId;
        ProductModel::update_product_db($productId,$product);
        return response()->json(['message' => "更新成功"], Response::HTTP_OK);
    }

    // 商品刪除
    public function delete(Request $request)
    {
        $productId = $request->productId;
        ProductModel::delete_product_db($productId);
        return response()->json(['message' => "刪除成功"], Response::HTTP_OK);
    }

}
