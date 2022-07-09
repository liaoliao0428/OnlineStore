<?php

namespace App\Http\Controllers\Frontend\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\OrderModel;
use App\Models\OrderDetailModel;

use App\Http\Controllers\Controller;

class OrderDetailApi extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('frontAuthCheck');
    // }

    // 訂單寫入資料庫
    public function insert($orderDetail)
    {
        OrderDetailModel::insert_order_detail_db($orderDetail);
    }

    // 訂單狀態更新
    public function update()
    {
        
    }
}
