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
        'description',
        'sort'
    ];
}

class ProductModel 
{
    // 取得全部商品
    public static function select_product_db()
    {
        return DB::select("SELECT * FROM product");
    }

    // 取得指定分類商品
    public static function select_product_with_categoryId_db($categoryId)
    {
        return DB::select("SELECT * FROM product WHERE categoryId = '$categoryId' ORDER BY sort ASC");
    }

    // 取得指定商品
    public static function select_product_with_productId_db($productId)
    {
        return DB::select("SELECT * FROM product WHERE productId = '$productId'");
    }

    // 取得商品細項
    public static function select_product_detail_with_productId_db($productId)
    {
        return DB::select("SELECT * FROM product_detail WHERE productId = '$productId'");
    }

    // 商品新增寫入
    public static function insert_product_db($product)
    {
        Product::create($product);
    }

    // 商品更新
    public static function update_product_db($productId,$product)
    {
        Product::where('productId',$productId)->update($product);
    }

    // 商品刪除
    public static function delete_product_db($productId)
    {
        Product::where('productId',$productId)->delete();
    }

    // 商品上下架
    public static function update_product_enable_db($productId,$enable)
    {
        Product::where('productId',$productId)->update(['enable'=>$enable]);
    }

    // 商品改排序
    public static function update_product_sort_db($productId,$sort)
    {
        Product::where('productId',$productId)->update(['sort'=>$sort]);
    }
}
