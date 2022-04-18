<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AdminModel extends Model
{
    //指定資料表
    protected $table = 'admin';

    //關閉自動時間戳
    public $timestamps = false;  

    /**
     * 可以被批量賦值的屬性
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'adminName',
        'account',
        'password',
        'accessToken',
        'createTime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    // protected $hidden = [
    //     'password',
    //     'remember_token',
    // ];

    //搜尋有沒有此帳號
    public static function select_admin_password_db($account,$password)
    {
        return DB::select("SELECT password FROM admin WHERE account = '$account'");
        // return DB::select('*')->find(1);
    }
}
