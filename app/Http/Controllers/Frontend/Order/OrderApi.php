<?php

namespace App\Http\Controllers\Frontend\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\OrderModel;
use App\Models\OrderDetailModel;

use App\Http\Controllers\Controller;

class OrderApi extends Controller
{
    public function __construct()
    {
        $this->middleware('frontAuthCheck');
    }

    // 訂單寫入資料庫
    public function insert($order)
    {
        OrderModel::insert_order_db($order);
    }

    // 訂單狀態更新
    public function update($orderNumber , $order)
    {
        OrderModel::update_order_db($orderNumber , $order);
    }

    // 撈指定會員所有訂單
    public function order(Request $request)
    {
        $userId = $request->userId;
        
        // 前台要的訂單狀態 1->全部、2->待付款、3->待出貨、4->待收貨、5->完成、6->不成立 跟資料庫的訂單狀態比對
        $orderStateActive = $request->orderStateActive;

        switch($orderStateActive){
            case 1:
                $orders = OrderModel::select_order_where_userId_db($userId);
            break;

            case 2:
                $orders = OrderModel::select_order_where_userId_payStatus_orderStatus_db($userId , 0 , 1);
            break;

            case 3:
                $orderStatus1 = OrderModel::select_order_where_userId_payStatus_orderStatus_db($userId , 1 , 1);
                $orderStatus2 = OrderModel::select_order_where_userId_payStatus_orderStatus_db($userId , 1 , 2);
                $orders = array_merge($orderStatus1,$orderStatus2);
            break;

            case 4:
                $orderStatus3 = OrderModel::select_order_where_userId_payStatus_orderStatus_db($userId , 1 , 3);
                $orderStatus4 = OrderModel::select_order_where_userId_payStatus_orderStatus_db($userId , 1 , 4);
                $orders = array_merge($orderStatus3,$orderStatus4);
            break;

            case 5:
                $orders = OrderModel::select_order_where_userId_payStatus_orderStatus_db($userId , 1 , 5);
            break;

            case 6:
                $orderStatus6 = OrderModel::select_order_where_userId_orderStatus_db($userId , 6);
                $orderStatus7 = OrderModel::select_order_where_userId_orderStatus_db($userId , 7);
                $orderStatus8 = OrderModel::select_order_where_userId_orderStatus_db($userId , 8);
                $orderStatus9 = OrderModel::select_order_where_userId_orderStatus_db($userId , 9);
                $orders = array_merge($orderStatus6 , $orderStatus7 , $orderStatus8 , $orderStatus9);
            break;
        }

        $this->setOrderEveryStatusName($orders);

        // 回傳資料
        if(!empty($orders)){
            return response()->json(['order' => $orders], Response::HTTP_OK);
        }else{
            return response()->json(['order' => null], Response::HTTP_OK);
        }
    }

    // 訂單狀態名稱轉換
    public function setOrderEveryStatusName($orders)
    {

        foreach($orders as $order){
            $orderStatus = $order->orderStatus;
            switch($orderStatus){
                case 1:
                    $order->orderStatusName = '確認中';
                break;

                case 2:
                    $order->orderStatusName = '訂單成立處理中';
                break;

                case 3:
                    $order->orderStatusName = '已出貨';
                break;

                case 4: 
                    $order->orderStatusName = '已送達';
                break;

                case 5: 
                    $order->orderStatusName = '訂單完成';
                break;

                case 6: 
                    $order->orderStatusName = '訂單取消申請中';
                break;

                case 7: 
                    $order->orderStatusName = '訂單退貨申請中';
                break;

                case 8: 
                    $order->orderStatusName = '訂單取消';
                break;

                case 9: 
                    $order->orderStatusName = '訂單退貨';
                break;
            }

            $receiverStoreType = $order->receiverStoreType;
            switch($receiverStoreType){
                case 'FAMI': case 'FAMIC2C':
                    $order->receiverStoreTypeName = '全家';
                break;

                case 'UNIMART': case 'UNIMARTFREEZE': case 'UNIMARTC2C':
                    $order->receiverStoreTypeName = '7-11';
                break;

                case 'HILIFE': case 'HILIFEC2C': case 'OKMARTC2C':
                    $order->receiverStoreTypeName = '萊爾富';
                break;

                case 'OKMARTC2C': 
                    $order->receiverStoreTypeName = 'OK';
                break;
            }

            $payStatus = $order->payStatus;
            switch($payStatus){
                case 0:
                    $order->payStatusName = '未付款';
                break;

                case 1:
                    $order->payStatusName = '已付款';
                break;

                case 2:
                    $order->payStatusName = '已退款';
                break;
            }

        }
    }

    // 取得已經建立訂單但是未結帳的訂單的productDetailId
    public function getOrderDetailIdNotPay(Request $request)
    {
        $orderNumber = $request->orderNumber;

        $orderDetails = OrderDetailModel::select_order_detail_db($orderNumber);

        $productDetailIds = [];
        foreach($orderDetails as $orderDetail){
            $productDetailId = $orderDetail->productDetailId;
            $productDetailIds[]['productDetailId'] = $productDetailId;
        }

        return response()->json(['productDetailId' => $productDetailIds], Response::HTTP_OK);
    }

    // 訂單完成
    public function finishOrder(Request $request)
    {
        $orderNumber = $request->orderNumber;

        $order['orderStatus'] = 5;
        OrderModel::update_order_db($orderNumber , $order);      
        
        return response()->json([true], Response::HTTP_OK);
    }

    // 取消訂單申請
    public function cancelOrderApply(Request $request)
    {
        $orderNumber = $request->orderNumber;

        $order['orderStatus'] = 6;
        OrderModel::update_order_db($orderNumber , $order);      
        
        return response()->json([true], Response::HTTP_OK);
    }

    // 訂單退貨申請
    public function returnOrderApply(Request $request)
    {
        $orderNumber = $request->orderNumber;

        $order['orderStatus'] = 7;
        OrderModel::update_order_db($orderNumber , $order);      
        
        return response()->json([true], Response::HTTP_OK);
    }

}
