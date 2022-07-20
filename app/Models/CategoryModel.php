<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Category extends Model
{
    //指定資料表
    protected $table = 'category';

    //時間戳欄位
    const CREATED_AT = 'createTime';
    const UPDATED_AT = 'updateTime';

    /**
     * 可以被批量賦值的屬性
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'categoryId',
        'categoryName'
    ];
}

class CategoryModel
{
    //撈全部分類
    public static function select_category_db()
    {
        return DB::select("SELECT categoryId , categoryName FROM category");
    }

    // 撈指定分類
    public static function select_category_where_categoryId_db($categoryId)
    {
        return DB::select("SELECT categoryName FROM category WHERE categoryId = '$categoryId'");
    }
}
