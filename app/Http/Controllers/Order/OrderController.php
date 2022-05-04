<?php

namespace App\Http\Controllers\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\OrderModel;
use App\Http\Traits\ToolTrait;
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


}
