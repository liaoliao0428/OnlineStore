<?php

namespace App\Http\Controllers\Frontend\Checkout;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Controllers\Frontend\Cart\CartApi;
use App\Http\Controllers\Frontend\User\UserReceiveAddressApi;

use App\Http\Controllers\Frontend\Order\OrderApi;
use App\Http\Controllers\Frontend\Order\OrderDetailApi;

use App\Models\CartModel;

use App\Models\OrderModel;
use App\Models\OrderDetailModel;

use App\Models\ProductModel;
use App\Models\ProductDetailModel;

use App\Models\UserReceiveAddressModel;

use App\Http\Traits\LinepayTrait;
use App\Http\Traits\Ecpay\PaymentTrait;
use App\Http\Traits\Ecpay\InvoiceTrait;
use App\Http\Traits\Ecpay\LogisticsTrait;

use App\Http\Controllers\Controller;

class CheckoutApi extends Controller
{
    public function __construct()
    {
        $this->middleware('frontAuthCheck')->except('ecpayPaymentCheckoutResponse' , 'ecpayLogisticsResponse' , 'linepayConfirm');
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

    // 取得使用者目前預設物流
    public function getReceiverDefaultAddress(Request $request)
    {
        $userId = $request->userId;

        $receiverDefaultAddress = UserReceiveAddressModel::select_user_receive_address_default_db($userId);

        // 判斷物流類型
        $userReceiveAddressApi = new UserReceiveAddressApi();
        $userReceiveAddressApi->setreceiverStoreType($receiverDefaultAddress);

        if(!empty($receiverDefaultAddress)){
            return response()->json(['receiverDefaultAddress' => $receiverDefaultAddress[0]], Response::HTTP_OK);  
        }else{
            return response()->json(['receiverDefaultAddress' => null], Response::HTTP_OK);  
        }
    }

    // 結帳
    public function checkout(Request $request)
    {
        $userId = $request->userId;       
        $checkoutType = $request->checkoutType;   // 結帳型態 1->無訂單 先建立單在結帳、2->有訂單 直接抓訂單編號結帳
        
        switch($checkoutType){
            case 1:
                return $this->checkoutNoOrder($userId , $request);
            break;

            case 2:
                return $this->checkoutWithOrder($userId , $request);
            break;
        }
    }

    // 無訂單結帳 先建立訂單在跑結帳
    public function checkoutNoOrder($userId , $request)
    {
        $totalPrice = $request->totalPrice;
        $checkoutProducts = $request->checkoutProducts;
        $receiveAddressId = $request->receiveAddressId;
        $payMedhod = $request->payMedhod;   // 付款方式 

        // 訂單寫入資料庫
        $orderNumber = $this->insertOrder($userId , $totalPrice , $receiveAddressId);
        // 訂單細項寫入資料庫
        $this->insertOrderDetail($userId , $orderNumber , $checkoutProducts);
        // 付款
        return $this->pay($userId , $payMedhod , $orderNumber);             
    }

    // 訂單寫入資料庫
    public function insertOrder($userId , $totalPrice , $receiveAddressId)
    {
        $orderNumber = time();
        $order['orderNumber'] = $orderNumber;
        $order['userId'] = $userId;
        $order['taxType'] = 1;
        $order['deliveryFee'] = 60;
        $order['amount'] = $totalPrice;
        $order['orderStatus'] = 1;

        $receiveAddress = UserReceiveAddressModel::selete_user_receive_address_where_receiveAddressId_db($receiveAddressId);
        $order['receiverName'] = $receiveAddress[0]->receiverName;
        $order['receiverCellPhone'] = $receiveAddress[0]->receiverCellPhone;
        $order['receiverStoreType'] = $receiveAddress[0]->receiverStoreType;
        $order['receiverStoreName'] = $receiveAddress[0]->receiverStoreName;
        $order['receiverStoreID'] = $receiveAddress[0]->receiverStoreID;

        $orderApi = new OrderApi();
        $orderApi->insert($order);

        return $orderNumber;
    }

    // 訂單細項寫入資料庫 並刪除庫存
    public function insertOrderDetail($userId , $orderNumber , $checkoutProducts)
    {
        // 組合訂單細項陣列
        $orderDetails = $this->setOrderDetails($userId , $orderNumber ,  $checkoutProducts);
        
        $OrderDetailApi = new OrderDetailApi();
        $OrderDetailApi->insert($orderDetails);

        // 並刪除庫存
        $this->deleteProductDetailQuantity($orderNumber);
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
    
    // 刪除庫存
    public function deleteProductDetailQuantity($orderNumber)
    {
        // 撈已經寫入訂單的商品
        $orderDetails = OrderDetailModel::select_order_detail_db($orderNumber);
        foreach($orderDetails as $orderDetail){
            $productDetailId = $orderDetail->productDetailId;
            $quantity = $orderDetail->quantity;

            // 撈product_detail表的商品數量
            $productDetail = ProductDetailModel::select_product_detail_with_productDetailId_db($productDetailId);
            $productDetailQuantity = $productDetail[0]->quantity;
            $newProductDetailQuantity['quantity'] = (int)$productDetailQuantity - (int)$quantity;

            // 更新庫存
            ProductDetailModel::update_product_detail_db($productDetailId,$newProductDetailQuantity);
        }
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
        // 刪除已成立訂單的購物車商品
        $this->deleteCartProduct($userId , $orderNumber);

        // 付款 1->綠界信用卡、2->linepay
        switch ($payMedhod){
            case 1:
                return $this->ecpayPayment($orderNumber);
            break;

            case 2:
                return $this->linepay($orderNumber);
            break;
        }
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

    // 綠界結帳
    public function ecpayPayment($orderNumber)
    {
        $clientBackUrl = 'http://localhost:3000/user/order';

        $checkoutProducts = OrderDetailModel::select_order_detail_db($orderNumber);
        $order = OrderModel::select_order_where_orderNumber_db($orderNumber);

        // 組合綠界結帳需要商品字串
        $itemName = $this->setItemName($checkoutProducts);

        $ecpayPaymentHtml = PaymentTrait::aioCheckOut($orderNumber , $order[0]->amount , $itemName ,  $clientBackUrl);

        return [
            'payType' => 1,
            'ecpayPaymentHtml' => $ecpayPaymentHtml
        ];
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

    // 綠界結帳結果回傳
    public function ecpayPaymentCheckoutResponse(Request $request)
    {
        $orderNumber = $request->MerchantTradeNo;
        $RtnCode = $request->RtnCode;

        // $RtnCode == 1 代表付款成功 更新復付款狀態以及時間
        if($RtnCode == 1){
            $order['payStatus'] = 1;
            $order['payMethod'] = $request->PaymentType;
            $order['payTime'] = $request->PaymentDate;

            // 結帳成功開發票、建立物流訂單
            $this->paySuccess($orderNumber , $order);
        }

        return '1|OK';
    }  
    
    // linepay結帳
    public function linepay($orderNumber)
    {       
        return LinepayTrait::checkout($orderNumber);
    }  

    // linepay 付款確認 要打到這支api 確認完付款後交易紀錄才會進到linepay後台
    public function linepayConfirm(Request $request , $orderNumber , $amount)
    { 
        $transactionId = $request->transactionId;

        $confirmResponse = LinepayTrait::confirm($transactionId , $amount);

        if($confirmResponse){
            $order['payStatus'] = 1;
            $order['payMethod'] = 'Linepay';
            $order['payTime'] = date('Y-m-d H:i:s');

            // 結帳成功開發票、建立物流訂單
            $this->paySuccess($orderNumber , $order);

            return redirect('http://localhost:3000/user/order');
        }
    }    

    // 結帳成功開發票跟建立物流訂單
    public function paySuccess($orderNumber , $order)
    {
        $orderApi = new OrderApi();
        $orderApi->update($orderNumber , $order);   // 更新付款狀態
        $this->generateInvoice($orderNumber);   // 付款成功開發票
        $this->generateEcpayLogisticsOrder($orderNumber);   // 付款成功建立物流訂單
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

    // 建立綠界物流訂單
    public function generateEcpayLogisticsOrder($orderNumber)
    {
        $order = OrderModel::select_order_where_orderNumber_db($orderNumber);
        $receiverStoreType = $order[0]->receiverStoreType;
        $amount = $order[0]->amount;
        $receiverName = $order[0]->receiverName;
        $receiverCellPhone = $order[0]->receiverCellPhone;
        $receiverStoreID = $order[0]->receiverStoreID;
        
        // 一段標測試 RtnCode = 1 代表測試成功 開始建立綠界物訂單
        $testData = LogisticsTrait::createTestData($receiverStoreType);
        if($testData['Data']['RtnCode'] == 1){

            // 建立綠界物流訂單
            $logisticsResponse = LogisticsTrait::create($orderNumber , $receiverStoreType , $amount , $receiverName , $receiverCellPhone , $receiverStoreID);
            $orderLogisticsData['ecpayLogisticsStatus'] = $logisticsResponse['RtnCode'];
            $orderLogisticsData['allPayLogisticsID'] = $logisticsResponse['1|AllPayLogisticsID'];

            $orderApi = new OrderApi();
            $orderApi->update($orderNumber , $orderLogisticsData);
        }
    }

    // 綠界物流狀態回傳
    public function ecpayLogisticsResponse(Request $request)
    {
        $orderNumber = $request->MerchantTradeNo;
        $orderLogisticsData['ecpayLogisticsStatus'] = $request->RtnCode;

        $orderApi = new OrderApi();
        $orderApi->update($orderNumber , $orderLogisticsData);

        return '1|OK';
    }          
}
