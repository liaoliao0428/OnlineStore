<?php

namespace App\Http\Controllers\Frontend\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\OrderModel;
use App\Models\OrderDetailModel;

use App\Http\Controllers\Controller;

class OrderApi extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('frontAuthCheck');
    // }

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
}
