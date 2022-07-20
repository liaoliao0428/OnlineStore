<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        'orderNumber',
        'userId',
        'orderStatus',
        'deliveryFee',
        'amount',
        'payMethod',
        'payTime',
        'invoiceNumber',
        'randomNumber',
        'invoiceDate',
        'taxType',
        'taxAmount',
        'invoiceDonate',
        'carrierType',
        'carrierId',
        'receiverName',
        'receiverCellPhone',
        'receiverStoreType',
        'receiverStoreName',
        'receiverStoreID',
        'ecpayLogisticsStatus',
        'allPayLogisticsID'
    ];
}

class OrderModel
{
    // 寫入
    public static function insert_order_db($order)
    {
        Order::create($order);
    }

    // 更新
    public static function update_order_db($orderNumber , $order)
    {
        Order::where('orderNumber',$orderNumber)->update($order);
    }

    // 抓指定會員全部訂單資料
    public static function select_order_where_userId_db($userId)
    {
        return DB::select("SELECT * 
                           FROM `order` 
                           WHERE userId = '$userId' 
                           ORDER BY createTime DESC");
    }

    // 抓指定會員指定付款狀態指定訂單狀態的訂單資料
    public static function select_order_where_userId_payStatus_orderStatus_db($userId , $payStatus , $orderStatus)
    {
        return DB::select("SELECT * 
                           FROM `order` 
                           WHERE userId = '$userId' AND payStatus = $payStatus AND orderStatus = $orderStatus 
                           ORDER BY createTime DESC");
    }

    // 抓指定會員狀態訂單資料
    public static function select_order_where_userId_orderStatus_db($userId , $orderStatus)
    {
        return DB::select("SELECT * 
                           FROM `order` 
                           WHERE userId = '$userId' AND orderStatus = $orderStatus 
                           ORDER BY createTime DESC");
    }

    // 抓指定狀態訂單資料
    public static function select_order_where_orderStatus($orderStatus)
    {
        return DB::select("SELECT * 
                           FROM `order` 
                           WHERE orderStatus = $orderStatus 
                           ORDER BY createTime DESC");
    }
    
    // 抓指定訂單編號訂單資料
    public static function select_order_where_orderNumber_db($orderNumber)
    {
        return DB::select("SELECT * 
                           FROM `order` 
                           WHERE orderNumber = '$orderNumber'");
    }

    
}
