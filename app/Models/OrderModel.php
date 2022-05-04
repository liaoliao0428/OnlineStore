<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //指定資料表
    protected $table = 'order';

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
        'orderNumber',
        'clientId',
        'invoiceHeader',
        'invoiceNumber',
        'randomNumber',
        'invoiceDate',
        'invoiceDateTaiwanType',
        'invoiceTime',
        'taxType',
        'invoiceDonate',
        'carrierType',
        'carrierId',
        'amount',
        'cashIncome',
        'cashChange',
        'salesAmount',
        'freeTaxSalesAmount',
        'totalAmount',
        'taxAmount',
        'zeroTaxSalesAmount',
        'payMethod',
        'receiveName',
        'receivePhone',
        'receiveAddress',
        'orderStatus'
    ];
}

class OrderModel
{
    
}
