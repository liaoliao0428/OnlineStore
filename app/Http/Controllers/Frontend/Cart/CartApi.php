<?php

namespace App\Http\Controllers\Frontend\Cart;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\CartModel;
use App\Models\ProductModel;
use App\Models\ProductDetailModel;
use App\Models\ProductImageModel;

class CartApi extends Controller
{
    public function __construct()
    {
        $this->middleware('frontAuthCheck')->except('setCartArray');
    }

    // 取得該使用者購物車資料
    public function cart(Request $request)
    {
        $userId = $request->userId;

        $carts = CartModel::select_cart_where_userId_db($userId);

        if(!empty($carts)){
            // 購物車陣列資料處理
            $this->setCartArray($carts);
            return response()->json(['cart' => $carts], Response::HTTP_OK);  
        }else{
            return response()->json(['cart' => null], Response::HTTP_OK);  
        }
    }
    
    // 購物車陣列資料處理
    public function setCartArray($carts)
    {
        foreach($carts as $cart){
            $productDetailId = $cart->productDetailId;
            $productDetail = ProductDetailModel::select_product_detail_with_productDetailId_db($productDetailId);
            $productId = $productDetail[0]->productId;
            $product = ProductModel::select_product_with_productId_db($productId);
            $productImage = ProductImageModel::select_product_image_with_productId_one_db($productId);

            $cart->unitPrice = (int)$productDetail[0]->unitPrice;
            $cart->productName = $product[0]->productName . '-' . $productDetail[0]-> productDetailName;  
            $cart->specification = $productDetail[0]-> specification; 
            $cart->image = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . "/OnlineStore/Backend/storage/app/public/productImage/" . $productId . '/' . $productImage[0]->image;
        }

        return $carts;
    }

    // 購物車新增資料
    public function insert(Request $request)
    {
        $cart['userId'] = $request->userId;
        $cart['productDetailId'] = $request->productDetailId;
        $cart['quantity'] = (int)$request->quantity;

        // 購物車新增資料
        CartModel::insert_cart_db($cart);
        return response()->json([true], Response::HTTP_OK);  
    }

    // 更新購物車資料
    public function update(Request $request)
    {
        
    }

    // 刪除購物車資料
    public function delete(Request $request)
    {
        $userId = $request->userId;
        $productDetailId = $request->productDetailId;

        // 刪除購物車資料
        CartModel::delete_cart_db($userId , $productDetailId);

        return response()->json([true], Response::HTTP_OK);          
    }
    
    // 成立訂單後刪除購物車資料
    public function deleteCartProduct($userId , $checkoutProduct)
    {
        // 刪除購物車資料
        CartModel::delete_cart_db($userId , $checkoutProduct);
    }
}
