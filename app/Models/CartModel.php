<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Cart extends Model
{
    //指定資料表
    protected $table = 'cart';

    //時間戳欄位
    const CREATED_AT = 'createTime';
    const UPDATED_AT = 'updateTime';

    /**
     * 可以被批量賦值的屬性
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'userId',
        'productDetailId',
        'quantity'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        
    ];
}

class CartModel
{
    // 購物車新增資料
    public static function insert_cart_db($cart)
    {
        Cart::create($cart);
    }

    // 刪除購物車資料
    public static function delete_cart_db($userId , $productDetailId)
    {
        Cart::where('userId',$userId)->where('productDetailId',$productDetailId)->delete();
    }

    // 撈購物車資料
    public static function select_cart_where_userId_db($userId)
    {
        return DB::select("SELECT productDetailId , SUM(quantity) as quantity
                           FROM cart 
                           WHERE userId = '$userId'
                           GROUP BY productDetailId 
                           ORDER BY createTime DESC");
    }
}
