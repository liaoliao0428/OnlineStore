<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrderDetail extends Model
{
    //指定資料表
    protected $table = 'order_detail';

    //時間戳欄位
    const CREATED_AT = 'createTime';
    const UPDATED_AT = 'updateTime';

    /**
     * 可以被批量賦值的屬性
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'orderNumber',
        'productDetailId',
        'categoryName',
        'productName',
        'specification',
        'unitPrice',
        'quantity',
        'amount',
        'unitPriceNoTax',
        'taxAmount'
    ];
}

class OrderDetailModel 
{
    // 新增
    public static function insert_order_detail_db($orderDetail)
    {
        OrderDetail::insert($orderDetail);
    }

    // 撈訂單細項商品細項id
    public static function select_order_detail_db($orderNumber)
    {
        return DB::select("SELECT * 
                           FROM order_detail 
                           WHERE orderNumber = '$orderNumber'");
    }   
}
