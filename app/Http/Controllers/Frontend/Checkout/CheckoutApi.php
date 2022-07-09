<?php

namespace App\Http\Controllers\Frontend\Checkout;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Controllers\Frontend\Cart\CartApi;

use App\Models\CartModel;
use App\Models\ProductModel;
use App\Models\ProductDetailModel;
use App\Models\ProductImageModel;

use App\Http\Controllers\Controller;

class CheckoutApi extends Controller
{
    public function __construct()
    {
        $this->middleware('frontAuthCheck');
    }

    // 取得要結帳的資料
    public function checkoutProduct(Request $request)
    {
        $userId = $request->userId;
        $checkoutPorudctDetailIds = $request->checkoutPorudctDetailIds;

        $checkoutProducts = [];

        $CartApi = new CartApi();
        foreach($checkoutPorudctDetailIds as $checkoutProduct){
            $productDetailId = $checkoutProduct['productDetailId'];
            $carts = CartModel::select_cart_where_userId_productDetailId_db($userId , $productDetailId);
            // 購物車陣列資料處理 並塞回要結帳的資料中
            $carts = $CartApi->setCartArray($carts);
            $checkoutProducts[] = $carts[0];
        }

        if(!empty($checkoutProducts)){
            return response()->json(['checkoutProducts' => $checkoutProducts], Response::HTTP_OK);  
        }else{
            return response()->json(['checkoutProducts' => null], Response::HTTP_OK);  
        }
    }
}
