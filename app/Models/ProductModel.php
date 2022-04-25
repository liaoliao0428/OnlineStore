<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    //指定資料表
    protected $table = 'product';

    //時間戳欄位
    const CREATED_AT = 'createTime';
    const UPDATED_AT = 'updateTime';

    /**
     * 可以被批量賦值的屬性
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'productId',
        'productName',
        'categoryId',
        'describtion',
    ];
}

class ProductModel extends Model
{
    //取得商品
    public static function select_product_with_categoryId_db($categoryId)
    {
        return DB::select("SELECT * FROM product WHERE categoryId = $categoryId");
    }
}
