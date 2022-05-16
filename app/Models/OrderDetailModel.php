<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'orderId',
        'productDetailId',
        'unitPrice',
        'quantity',
        'amount',
        'unitPriceNoTax',
        'taxAmount'
    ];
}

class OrderDetailModel 
{
    
}
