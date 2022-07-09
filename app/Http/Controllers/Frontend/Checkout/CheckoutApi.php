<?php

namespace App\Http\Controllers\Frontend\Checkout;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Controllers\Frontend\Cart\CartApi;

use App\Http\Controllers\Frontend\Order\OrderApi;
use App\Http\Controllers\Frontend\Order\OrderDetailApi;

use App\Models\CartModel;
// use App\Models\ProductModel;
// use App\Models\ProductDetailModel;
// use App\Models\ProductImageModel;

use App\Http\Traits\Ecpay\PaymentTrait;
use App\Http\Traits\ToolTrait;

use App\Http\Controllers\Controller;

class CheckoutApi extends Controller
{
    public function __construct()
    {
        $this->middleware('frontAuthCheck')->except('ecpayPaymentCheckoutResponse');
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

    // 結帳
    public function checkout(Request $request)
    {
        $userId = $request->userId;
        $totalPrice = $request->totalPrice;
        $checkoutProducts = $request->checkoutProducts;
        $payMedhod = $request->payMedhod;
        $clientBackUrl = 'http://localhost:3000/user/order';
        
        // 訂單寫入資料庫
        $orderIdentify = $this->insertOrder($userId , $totalPrice);
        // 訂單細項寫入資料庫
        $this->insertOrderDetail($userId , $orderIdentify['orderId'] , $checkoutProducts);
        

        // 付款 1->綠界信用卡、2->linepay
        switch ($payMedhod){
            case 1:
                $this->ecpayPayment($orderIdentify['orderNumber'] , $userId , $checkoutProducts , $totalPrice , $clientBackUrl);
            break;

            case 2:
                $this->linepay();
            break;
        }       
        
        // 刪除已成立訂單的購物車商品
        $this->deleteCartProduct($userId , $checkoutProducts);
    }

    // 綠界結帳
    public function ecpayPayment($orderNumber , $userId , $checkoutProducts , $totalPrice , $clientBackUrl)
    {
        $CartApi = new CartApi();
        $itemName = '';
        foreach($checkoutProducts as $checkoutProduct){
            $productDetailId = $checkoutProduct['productDetailId'];
            $carts = CartModel::select_cart_where_userId_productDetailId_db($userId , $productDetailId);
            // 購物車陣列資料處理 並塞回要結帳的資料中
            $carts = $CartApi->setCartArray($carts);
            // 組合綠界結帳要的商品字串
            $itemName = $itemName . $carts[0]->productName . ' ' . $carts[0]->unitPrice . '元' . ' ' . 'x' . $carts[0]->quantity . '#';
        }

        $returnUrl = 'http://192.168.1.106/OnlineStore/Backend/public/api/checkout/ecpayPaymentCheckoutResponse'; // 訂單付款狀態response

        PaymentTrait::aioCheckOut($orderNumber , $itemName , $totalPrice , $returnUrl , $clientBackUrl);
    }

    // 綠界結帳結果回傳
    public function ecpayPaymentCheckoutResponse(Request $request)
    {
        $orderNumber = $request->MerchantTradeNo;
        $RtnCode = $request->RtnCode;
        $order['payMethod'] = $request->PaymentDate;
        $order['payTime'] = $request->PaymentType;

        // $RtnCode == 1 代表付款成功 更新復付款狀態以及時間
        if($RtnCode == 1){
            $orderApi = new OrderApi();
            $orderApi->update($orderNumber , $order);
        }

        return 1;
    }

    // linepay結帳
    public function linepay()
    {

    }

    // 訂單寫入資料庫
    public function insertOrder($userId , $totalPrice)
    {
        $orderId = ToolTrait::randomString(13);
        $orderNumber = time();
        $order['orderId'] = $orderId;
        $order['orderNumber'] = $orderNumber;
        $order['userId'] = $userId;
        $order['taxType'] = 1;
        $order['deliveryFee'] = 60;
        $order['amount'] = $totalPrice;
        $order['orderStatus'] = 1;

        // 下兩個欄位測試用 等掛上網域可以接收綠type後刪除
        $order['payMethod'] = 1;
        $order['payTime'] = date('Y-m-d H:i:s');
        // 上兩個欄位測試用 等掛上網域可以接收綠type後刪除

        $orderApi = new OrderApi();
        $orderApi->insert($order);

        return [
            'orderId' => $orderId,
            'orderNumber' => $orderNumber
        ];
    }

    // 訂單細項寫入資料庫
    public function insertOrderDetail($userId , $orderId , $checkoutProducts)
    {
        $orderDetail['orderId'] = $orderId;
        $orderDetail['createTime'] = date('Y-m-d H:i:s');
        $orderDetail['updateTime'] = date('Y-m-d H:i:s');

        $CartApi = new CartApi();
        $orderDetails = [];
        foreach($checkoutProducts as $checkoutProduct){
            $productDetailId = $checkoutProduct['productDetailId'];
            $carts = CartModel::select_cart_where_userId_productDetailId_db($userId , $productDetailId);
            // 購物車陣列資料處理 並塞入要寫入訂單細項的資料中
            $carts = $CartApi->setCartArray($carts);
            $orderDetail['productDetailId'] = $carts[0]->productDetailId;
            $orderDetail['unitPrice'] = (int)$carts[0]->unitPrice;
            $orderDetail['quantity'] = (int)$carts[0]->quantity;
            $orderDetail['amount'] = (int)$carts[0]->unitPrice * (int)$carts[0]->quantity;
            $orderDetails[] = $orderDetail;
        }
        
        $OrderDetailApi = new OrderDetailApi();
        $OrderDetailApi->insert($orderDetails);
    }


    // 刪除已成立訂單的購物車商品
    public function deleteCartProduct($userId , $checkoutProducts)
    {
        $CartApi = new CartApi();
        foreach($checkoutProducts as $checkoutProduct)
        {
            $CartApi->deleteCartProduct($userId , $checkoutProduct);
        }        
    }
}
