<?php

namespace App\Http\Controllers\Frontend\Checkout;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Controllers\Frontend\Cart\CartApi;

use App\Http\Controllers\Frontend\Order\OrderApi;
use App\Http\Controllers\Frontend\Order\OrderDetailApi;

use App\Models\CartModel;

use App\Models\OrderModel;
use App\Models\OrderDetailModel;

use App\Models\ProductModel;
use App\Models\ProductDetailModel;

use App\Http\Traits\Ecpay\PaymentTrait;
use App\Http\Traits\Ecpay\InvoiceTrait;

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
        $checkoutType = $request->checkoutType;   // 結帳型態 1->無訂單 先結單
        
        switch($checkoutType){
            case 1:
                $this->checkoutNoOrder($userId , $request);
            break;

            case 2:
                $this->checkoutWithOrder($userId , $request);
            break;
        }
    }

    // 無訂單結帳 先建立訂單在跑結帳
    public function checkoutNoOrder($userId , $request)
    {
        $totalPrice = $request->totalPrice;
        $checkoutProducts = $request->checkoutProducts;
        $payMedhod = $request->payMedhod;   // 付款方式 

        // 訂單寫入資料庫
        $orderNumber = $this->insertOrder($userId , $totalPrice);
        // 訂單細項寫入資料庫
        $this->insertOrderDetail($userId , $orderNumber , $checkoutProducts);
        // 付款
        $this->pay($userId , $payMedhod , $orderNumber);             
    }

    // 訂單寫入資料庫
    public function insertOrder($userId , $totalPrice)
    {
        $orderNumber = time();
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

        return $orderNumber;
    }

    // 訂單細項寫入資料庫
    public function insertOrderDetail($userId , $orderNumber , $checkoutProducts)
    {
        $orderDetail['orderNumber'] = $orderNumber;
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

    // 有訂單結帳
    public function checkoutWithOrder($userId , $request)
    {
        $orderNumber = $request->orderNumber;
        $payMedhod = $request->payMedhod;   // 付款方式 

        // 付款
        $this->pay($userId , $payMedhod , $orderNumber); 
    }

    // 付款
    public function pay($userId , $payMedhod , $orderNumber)
    {
        $clientBackUrl = 'http://localhost:3000/user/order';

        // 付款 1->綠界信用卡、2->linepay
        switch ($payMedhod){
            case 1:
                $this->ecpayPayment($orderNumber , $clientBackUrl);
            break;

            case 2:
                $this->linepay();
            break;
        }
        
        // 刪除已成立訂單的購物車商品
        // $this->deleteCartProduct($userId , $orderNumber);
    }

    // 綠界結帳
    public function ecpayPayment($orderNumber , $clientBackUrl)
    {
        $itemName = '';
        $checkoutProducts = OrderDetailModel::select_order_detail_db($orderNumber);
        $order = OrderModel::select_order_where_orderNumber_db($orderNumber);
        foreach($checkoutProducts as $checkoutProduct){
            $productDetailId = $checkoutProduct->productDetailId;
            $productDetail = ProductDetailModel::select_product_detail_with_productDetailId_db($productDetailId);
            $productId = $productDetail[0]->productId;
            $product = ProductModel::select_product_with_productId_db($productId);
            // 組合綠界結帳要的商品字串
            $itemName = $itemName . $product[0]->productName . ' - ' . $productDetail[0]->productDetailName . ' ' . $checkoutProduct->unitPrice . '元' . ' ' . 'x' . $checkoutProduct->quantity . '#';
        }

        $returnUrl = 'http://192.168.1.106/OnlineStore/Backend/public/api/checkout/ecpayPaymentCheckoutResponse'; // 訂單付款狀態response

        PaymentTrait::aioCheckOut($orderNumber , $itemName , $order[0]->amount , $returnUrl , $clientBackUrl);
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

        return '1|OK';
    }    

    // linepay結帳
    public function linepay()
    {

    }

    // 成功開發票
    public function generateInvoice($orderNumber)
    {
        InvoiceTrait::Issue();
    }

    // 刪除已成立訂單的購物車商品
    public function deleteCartProduct($userId , $orderNumber)
    {
        $CartApi = new CartApi();
        $checkoutProducts = OrderDetailModel::select_order_detail_db($orderNumber);
        foreach($checkoutProducts as $checkoutProduct)
        {
            $CartApi->deleteCartProduct($userId , $checkoutProduct->productDetailId);
        }        
    }
}
