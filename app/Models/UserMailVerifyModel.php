<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class UserMailVerify extends Model
{
    //指定資料表
    protected $table = 'user_mail_verify';

    //時間戳欄位
    const CREATED_AT = 'createTime';
    const UPDATED_AT = 'updateTime';

    /**
     * 可以被批量賦值的屬性
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'mail',
        'verifyNumberEncryption',
        'experTime'
    ];
}

class UserMailVerifyModel
{
    // 寫入信箱驗證碼
    public static function insert_user_mail_verify_db($userMailVerify)
    {
        UserMailVerify::create($userMailVerify);
    }    

    // 刪除信箱
    public static function delete_user_mail_verify_where_mail_db($mail)
    {
        UserMailVerify::where('mail',$mail)->delete();
    }

    // 抓指定信箱驗證加密
    public static function select_user_mail_verify_where_mail_db($mail)
    {
        return DB::select("SELECT * FROM user_mail_verify WHERE mail = '$mail' ORDER BY experTime DESC limit 1");
    }
}


