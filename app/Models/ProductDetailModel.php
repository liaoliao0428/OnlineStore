<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class ProductDetail extends Model
{
    //指定資料表
    protected $table = 'product_detail';

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
        'productDetailId',
        'productDetailName',
        'specification',
        'unitPrice',
        'quantity',
        'taxType',
        'unitPriceNoTax',
        'taxAmount',
        'sort'
    ];
}

class ProductDetailModel
{
    // 抓商品細項資料
    public static function select_product_detail_with_productId_db($productId)
    {
        return DB::select("SELECT * 
                           FROM product_detail 
                           WHERE productId = '$productId' 
                           ORDER BY sort ASC");
    }

    // 抓商品細項資料篩選掉沒上架的
    public static function select_product_detail_with_productId_enable_db($productId)
    {
        return DB::select("SELECT * 
                           FROM product_detail 
                           WHERE productId = '$productId' AND enable = 1 
                           ORDER BY sort ASC");
    }

    // 抓指定商品細項資料
    public static function select_product_detail_with_productDetailId_db($productDetailId)
    {
        return DB::select("SELECT * 
                           FROM product_detail 
                           WHERE productDetailId = '$productDetailId'");
    }

    // 抓指定商品細項價錢(最便宜的)
    public static function select_product_detail_price_with_productId_db($productId)
    {
        return DB::select("SELECT unitPrice 
                           FROM product_detail 
                           WHERE productId = '$productId' AND enable = 1 AND quantity > 0 
                           ORDER BY unitPrice ASC 
                           LIMIT 1 ");
    }

    // 商品子項寫入
    public static function insert_product_detail_db($productDetail)
    {
        ProductDetail::create($productDetail);
    }

    // 商品子項上下架
    public static function update_product_detail_enable_db($productDetailId,$enable)
    {
        ProductDetail::where('productDetailId',$productDetailId)->update(['enable'=>$enable]);
    }

    // 商品子項更新
    public static function update_product_detail_db($productDetailId,$productDetail)
    {
        ProductDetail::where('productDetailId',$productDetailId)->update($productDetail);
    }

    // 商品子項刪除
    public static function delete_product_detail_db($productDetailId)
    {
        ProductDetail::where('productDetailId',$productDetailId)->delete();
    }

    // 商品子項改排序
    public static function update_product_detail_sort_db($productDetailId,$sort)
    {
        ProductDetail::where('productDetailId',$productDetailId)->update(['sort'=>$sort]);
    }
}
