<?php

namespace App\Http\Controllers\Backend\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\OrderModel;
use App\Http\Traits\ToolTrait;
use App\Models\OrderDetailModel;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    use ToolTrait;
    //建構子 middleware設定
    public function __construct()
    {
        $this->middleware('authCheck');
    }

    // 訂單管理首頁
    public function index()
    {
        return view('backstage.order.index'); 
    }

    // 訂單詳情
    public function detail()
    {
        return view('backstage.order.detail'); 
    }

    // 抓指定狀態訂單
    public function order(Request $request)
    {
        // 後台要的訂單狀態 1->新訂單、2->處理中、3->已出貨、4->已送達、5->訂單完成、6->訂單取消/退貨申請、7->訂單取消/退貨 跟資料庫的訂單狀態比對
        $orderStatus = $request->orderStatus;

        switch($orderStatus){
            case 1:
                $orders = OrderModel::select_order_where_orderStatus(1);
            break;

            case 2:
                $orders = OrderModel::select_order_where_orderStatus(2);
            break;

            case 3:
                $orders = OrderModel::select_order_where_orderStatus(3);
            break;

            case 4:
                $orders = OrderModel::select_order_where_orderStatus(4);
            break;

            case 5:
                $orders = OrderModel::select_order_where_orderStatus(5);
            break;

            case 6:
                $orderStatus6 = OrderModel::select_order_where_orderStatus(6);
                $orderStatus7 = OrderModel::select_order_where_orderStatus(7);
                $orders = array_merge($orderStatus6 , $orderStatus7);
            break;

            case 7:
                $orderStatus8 = OrderModel::select_order_where_orderStatus(8);
                $orderStatus9 = OrderModel::select_order_where_orderStatus(9);
                $orders = array_merge($orderStatus8 , $orderStatus9);
            break;
        }

        // 訂單付款狀態、物流轉換
        $this->setOrderOrderStatusPaystatusReceiverStoreType($orders);

        // 回傳資料
        if(!empty($orders)){
            return response()->json(['order' => $orders], Response::HTTP_OK);
        }else{
            return response()->json(['order' => []], Response::HTTP_OK);
        }
    }

    // 訂單付款狀態、物流轉換
    public function setOrderOrderStatusPaystatusReceiverStoreType($orders)
    {

        foreach($orders as $order){
            $orderStatus = $order->orderStatus;
            switch($orderStatus){
                case 1:
                    $order->orderStatus = '確認中';
                break;

                case 2:
                    $order->orderStatus = '訂單成立處理中';
                break;

                case 3:
                    $order->orderStatus = '已出貨';
                break;

                case 4: 
                    $order->orderStatus = '已送達';
                break;

                case 5: 
                    $order->orderStatus = '訂單完成';
                break;

                case 6: 
                    $order->orderStatus = '訂單取消申請';
                break;

                case 7: 
                    $order->orderStatus = '訂單退貨申請';
                break;

                case 8: 
                    $order->orderStatus = '訂單取消';
                break;

                case 9: 
                    $order->orderStatus = '訂單退貨';
                break;
            }

            $receiverStoreType = $order->receiverStoreType;
            switch($receiverStoreType){
                case 'FAMI': case 'FAMIC2C':
                    $order->receiverStoreType = '全家';
                break;

                case 'UNIMART': case 'UNIMARTFREEZE': case 'UNIMARTC2C':
                    $order->receiverStoreType = '7-11';
                break;

                case 'HILIFE': case 'HILIFEC2C': case 'OKMARTC2C':
                    $order->receiverStoreType = '萊爾富';
                break;

                case 'OKMARTC2C': 
                    $order->receiverStoreType = 'OK';
                break;
            }

            $payStatus = $order->payStatus;
            switch($payStatus){
                case 0:
                    $order->payStatus = '未付款';
                break;

                case 1:
                    $order->payStatus = '已付款';
                break;

                case 2:
                    $order->payStatus = '已退款';
                break;
            }

        }
    }

    // 取得訂單詳細資料
    public function orderFullData(Request $request)
    {
        $orderNumber = $request->orderNumber;

        $order = OrderModel::select_order_where_orderNumber_db($orderNumber);
        $orderDetail = OrderDetailModel::select_order_detail_db($orderNumber);

        $orderFullData['order'] = $order[0];
        $orderFullData['orderDetail'] = $orderDetail;

        // 回傳資料
        if(!empty($order)){
            return response()->json(['orderFullData' => $orderFullData], Response::HTTP_OK);
        }else{
            return response()->json(['orderFullData' => null], Response::HTTP_OK);
        }
    }

    // 訂單成立
    public function confirmOrder(Request $request)
    {
        $orderNumber = $request->orderNumber;

        $order['orderStatus'] = 2;
        OrderModel::update_order_db($orderNumber , $order);
        return response()->json([true], Response::HTTP_OK);

    }

    // 取消訂單
    public function cancelOrder(Request $request)
    {
        $orderNumber = $request->orderNumber;

        $order['orderStatus'] = 8;
        OrderModel::update_order_db($orderNumber , $order);
        // 金流退款
        // 物流訂單取消
        // 庫存加回去        
        
        return response()->json([true], Response::HTTP_OK);
    }

    // 訂單退貨
    public function returnOrder(Request $request)
    {
        $orderNumber = $request->orderNumber;

        $order['orderStatus'] = 9;
        OrderModel::update_order_db($orderNumber , $order);
        // 金流退款
        // 物流訂單取消
        // 庫存加回去        
        
        return response()->json([true], Response::HTTP_OK);
    }
}
