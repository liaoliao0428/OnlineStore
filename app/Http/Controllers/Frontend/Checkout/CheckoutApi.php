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

        // 結帳商品陣列組合
        $checkoutProducts = $this->setCheckoutProducts($userId , $checkoutPorudctDetailIds);

        if(!empty($checkoutProducts)){
            return response()->json(['checkoutProducts' => $checkoutProducts], Response::HTTP_OK);  
        }else{
            return response()->json(['checkoutProducts' => null], Response::HTTP_OK);  
        }
    }

    // 結帳商品陣列組合
    public function setCheckoutProducts($userId , $checkoutPorudctDetailIds)
    {
        $checkoutProducts = [];

        $CartApi = new CartApi();
        foreach($checkoutPorudctDetailIds as $checkoutProduct){
            $productDetailId = $checkoutProduct['productDetailId'];
            $carts = CartModel::select_cart_where_userId_productDetailId_db($userId , $productDetailId);
            // 購物車陣列資料處理 並塞回要結帳的資料中
            $carts = $CartApi->setCartArray($carts);
            $checkoutProducts[] = $carts[0];
        }

        return $checkoutProducts;
    }

    // 結帳
    public function checkout(Request $request)
    {
        $userId = $request->userId;       
        $checkoutType = $request->checkoutType;   // 結帳型態 1->無訂單 先建立單在結帳、2->有訂單 直接抓訂單編號結帳
        
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

        $orderApi = new OrderApi();
        $orderApi->insert($order);

        return $orderNumber;
    }

    // 訂單細項寫入資料庫
    public function insertOrderDetail($userId , $orderNumber , $checkoutProducts)
    {
        // 組合訂單細項陣列
        $orderDetails = $this->setOrderDetails($userId , $orderNumber ,  $checkoutProducts);
        
        $OrderDetailApi = new OrderDetailApi();
        $OrderDetailApi->insert($orderDetails);
    }

    // 組合訂單細項陣列
    public function setOrderDetails($userId , $orderNumber , $checkoutProducts)
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

        return $orderDetails;
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
                $this->linepay($orderNumber , $clientBackUrl);
            break;
        }
        
        // 刪除已成立訂單的購物車商品
        $this->deleteCartProduct($userId , $orderNumber);
    }

    // 綠界結帳
    public function ecpayPayment($orderNumber , $clientBackUrl)
    {
        $checkoutProducts = OrderDetailModel::select_order_detail_db($orderNumber);
        $order = OrderModel::select_order_where_orderNumber_db($orderNumber);

        // 組合綠界結帳需要商品字串
        $itemName = $this->setItemName($checkoutProducts);

        PaymentTrait::aioCheckOut($orderNumber , $order[0]->amount , $itemName ,  $clientBackUrl);
    }

    // 組合綠界結帳需要商品字串
    public function setItemName($checkoutProducts)
    {
        $itemName = '';
        foreach($checkoutProducts as $checkoutProduct){
            $productDetailId = $checkoutProduct->productDetailId;
            $productDetail = ProductDetailModel::select_product_detail_with_productDetailId_db($productDetailId);
            $productId = $productDetail[0]->productId;
            $product = ProductModel::select_product_with_productId_db($productId);
            // 組合綠界結帳要的商品字串
            $itemName = $itemName . $product[0]->productName . ' - ' . $productDetail[0]->productDetailName . ' ' . $checkoutProduct->unitPrice . '元' . ' ' . 'x' . $checkoutProduct->quantity . '#';
        }

        return $itemName;
    }

    // linepay結帳
    public function linepay($orderNumber , $clientBackUrl)
    {

    }

    // 綠界結帳結果回傳
    public function ecpayPaymentCheckoutResponse(Request $request)
    {
        $orderNumber = $request->MerchantTradeNo;
        $RtnCode = $request->RtnCode;
        $order['payMethod'] = $request->PaymentType;
        $order['payTime'] = $request->PaymentDate;        

        // $RtnCode == 1 代表付款成功 更新復付款狀態以及時間
        if($RtnCode == 1){
            $orderApi = new OrderApi();
            $orderApi->update($orderNumber , $order);   // 更新付款狀態
            $this->generateInvoice($orderNumber);   // 付款成功開發票
        }

        return '1|OK';
    }    

    // 開立綠界發票
    public function generateInvoice($orderNumber)
    {
        $order = OrderModel::select_order_where_orderNumber_db($orderNumber);
        $orderDetailProducts = OrderDetailModel::select_order_detail_db($orderNumber);

        $salesAmount = $order[0]->amount - $order[0]->deliveryFee;

        // 組合開立綠界發票陣列
        $orderProductAarray = $this->setOrderProductArray($orderDetailProducts);

        // 綠界開發票回傳
        $invoiceData = InvoiceTrait::Issue($orderNumber , $salesAmount , $orderProductAarray);

        // 發票開立成功更新訂單發票資訊
        if($invoiceData['Data']['RtnCode'] == 1){
            $this->updateOrderInvoice($orderNumber);
        }
    }

    // 組合開立綠界發票陣列
    public function setOrderProductArray($orderDetailProducts)
    {
        $orderProductAarray = [];
        $orderProduct = [];
        foreach($orderDetailProducts as $index => $orderDetailProduct){
            $productDetailId = $orderDetailProduct->productDetailId;
            $productDetail = ProductDetailModel::select_product_detail_with_productDetailId_db($productDetailId);

            $orderProduct['ItemSeq'] = $index + 1;
            $orderProduct['ItemName'] = $productDetail[0]->productDetailName;
            $orderProduct['ItemCount'] = $orderDetailProduct->quantity;
            $orderProduct['ItemWord'] = $productDetail[0]->specification;
            $orderProduct['ItemPrice'] = $orderDetailProduct->unitPrice;
            $orderProduct['ItemTaxType'] = "1";
            $orderProduct['ItemAmount'] = $orderDetailProduct->quantity * $orderDetailProduct->unitPrice;
            $orderProductAarray[] = $orderProduct;
        }
        
        return $orderProductAarray;
    }

    // 更新訂單發票資訊
    public function updateOrderInvoice($orderNumber)
    {
        // 綠界取得發票資訊
        $invoiceData = InvoiceTrait::GetIssue($orderNumber);

        $order['invoiceNumber'] = $invoiceData['Data']['IIS_Number'];
        $order['randomNumber'] = $invoiceData['Data']['IIS_Random_Number'];
        $order['invoiceDate'] = $invoiceData['Data']['IIS_Create_Date'];
        $order['taxType'] = $invoiceData['Data']['IIS_Tax_Type'];
        $order['invoiceDonate'] = 0;
        $order['taxAmount'] = $invoiceData['Data']['IIS_Tax_Amount'];

        $orderApi = new OrderApi();
        $orderApi->update($orderNumber , $order);
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
