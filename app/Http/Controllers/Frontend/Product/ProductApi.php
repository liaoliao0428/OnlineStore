<?php

namespace App\Http\Controllers\Frontend\Product;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Traits\ToolTrait;

use App\Models\ProductModel;
use App\Models\ProductDetailModel;
use App\Models\ProductImageModel;

class ProductApi extends Controller
{
    use ToolTrait;
    //建構子 middleware設定
    public function __construct()
    {
        $this->middleware('authCheck');
    }

    // 前台取得商品
    public function productAll(Request $request)
    {
        if($request['categoryId']){
            $products = ProductModel::select_product_with_categoryId_db($request['categoryId']);
        }else{
            $products = ProductModel::select_product_db();
        }

        foreach($products as $index => $product){
            // 抓商品最低價錢
            $productId = $product->productId;
            $price = ProductDetailModel::select_product_detail_price_with_productId_db($productId);
            $products[$index]->price = $price[0]->unitPrice;

            // 抓商品圖片
            $image = ProductImageModel::select_product_image_with_productId_one_db($productId);
            $imageUrl = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . "/OnlineStore/Backend/storage/app/public/productImage/" . $productId . '/' . $image[0]->image;
            $products[$index]->imageUrl = $imageUrl;
        }

        return response()->json(['products' => $products], Response::HTTP_OK);
    }

    // 前台取得單格商品資料
    public function productDetail(Request $request)
    {
        // return $request['productId'];
        $productId = $request['productId'];
        $product = ProductModel::select_product_with_productId_db($productId);
        $detail = ProductDetailModel::select_product_detail_with_productId_enable_db($productId);
        
        $images = ProductImageModel::select_product_image_with_productId_db($productId);
        foreach($images as $index => $image){
            $image->image = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . "/OnlineStore/Backend/storage/app/public/productImage/" . $productId . '/' . $image->image;
        }

        $productDetail['product'] = $product[0];
        $productDetail['detail'] = $detail;
        $productDetail['images'] = $images;
        return response()->json(['productDetail' => $productDetail], Response::HTTP_OK);
    }
}
