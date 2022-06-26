<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class User extends Model
{
    //指定資料表
    protected $table = 'user';

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
        'userName',
        'userImage',
        'gender',
        'phone',
        'address',
        'mail',
        'birthDay',
        'lastLoginTime',
        'password',
        'accessToken',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        
    ];
}

class UserModel
{
    // 註冊訊息寫入資料庫
    public static function insert_user_db($user)
    {
        User::create($user);
    }

    // 更新資料
    public static function update_user_lastLoginTime_db($userId , $signinTime)
    {
        User::where('userId',$userId)->update(['lastLoginTime' => $signinTime]);
    }

    // 抓資料庫這個信箱有沒有資料
    public static function select_user_where_mail($mail)
    {
        return DB::select("SELECT * FROM user WHERE mail = '$mail'");
    }

    // 抓使用hash加密過的密碼
    public static function select_user_userId_password_where_mail_db($mail)
    {
        return DB::select("SELECT userId , password FROM user WHERE mail = '$mail'");
    }

    // 抓使用者頭像以及使用者名稱
    public static function select_user_userName_userImage_db($userId)
    {
        return DB::select("SELECT userName , userImage FROM user WHERE userId = '$userId'");
    }
}
