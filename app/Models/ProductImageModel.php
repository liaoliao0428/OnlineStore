<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProductImage extends Model
{
    //指定資料表
    protected $table = 'product_image';

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
        'imageId',
        'image',
        'sort'
    ];
}

class ProductImageModel 
{
    // 抓這個商品所有圖片
    public static function select_product_image_with_productId_db($productId)
    {
        return DB::select("SELECT * FROM product_image WHERE productId = '$productId' ORDER BY sort");
    }

    // 商品圖片資訊寫入
    public static function insert_product_image_db($productImage)
    {
        ProductImage::create($productImage);
    }

    // 商品圖片刪除
    public static function delete_product_image_db($image)
    {
        ProductImage::where('image',$image)->delete();
    }

    // 商品圖片改排序
    public static function update_product_image_sort_db($imageId,$sort)
    {
        ProductImage::where('imageId',$imageId)->update(['sort'=>$sort]);
    }

}
