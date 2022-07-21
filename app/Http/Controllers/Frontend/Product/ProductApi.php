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
        $this->middleware('authCheck')->except('search');
    }

    // 主頁搜尋商品關鍵字
    public function searchKeyword(Request $request)
    {
        $keyWord = $request->keyWord;

        if(!empty($keyWord)){
            $originKeyWord[] = [
                'productName' => $keyWord
            ];
            $searchProductName = ProductModel::select_product_productName_where_keyword($keyWord);
            $searchResult = array_merge($originKeyWord , $searchProductName);
        }

        // 回傳資料
        if(!empty($searchResult)){
            return response()->json(['searchResult' => $searchResult], Response::HTTP_OK);
        }else{
            return response()->json(['searchResult' => null], Response::HTTP_OK);
        }
    }

    // 前台取得商品
    public function productAll(Request $request)
    {
        $searchMode = 1;
        if($request['categoryId']){
            $searchMode = 2;
        }elseif($request['keyword']){
            $searchMode = 3;
        }

        switch($searchMode){
            case 1:
                $products = ProductModel::select_product_db();
            break;

            case 2:
                $products = ProductModel::select_product_with_categoryId_db($request['categoryId']);
            break;

            case 3:
                $products = ProductModel::select_product_where_keyword($request['keyword']);
            break;
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
