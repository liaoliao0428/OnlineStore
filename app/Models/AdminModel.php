<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Admin extends Model
{
    //指定資料表
    protected $table = 'admin';

    //時間戳欄位
    const CREATED_AT = 'createTime';
    const UPDATED_AT = 'updateTime';

    /**
     * 可以被批量賦值的屬性
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'adminName',
        'account',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'accessToken',
    ];
}


class AdminModel 
{
    //取得資料庫加密過後密碼
    public static function select_admin_password_db($account)
    {    
        return DB::select("SELECT password 
                           FROM admin 
                           WHERE account = '$account'");
    }

    //取得token
    public static function select_admin_data_db($account,$passwordEncryption)
    {   
        return DB::select("SELECT adminName , adminId , accessToken 
                           FROM admin 
                           WHERE account = '$account' AND password = '$passwordEncryption'");
    }

    //更新token
    public static function update_admin_token_db($account,$passwordEncryption,$newToken)
    {
        Admin::where('account', $account)->where('password', $passwordEncryption)->update(['accessToken' => $newToken]);
    }
}
