<?php

namespace App\Http\Controllers\Frontend\User;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

use App\Models\UserModel;
use App\Models\UserMailVerifyModel;

class UserMailVerifyApi extends Controller
{
    protected $mail;

    // 寄驗證碼信件
    public function sendVerifyMail(Request $request)
    {
        // 信箱存入物件 以便寄信function可以動態改變信箱
        $mail = $request->mail;
        $this->mail = $mail;

        // 判斷信箱是否已註冊過
        $mailExist = UserModel::select_user_where_mail($mail);
        if($mailExist){
            return response()->json(['mailExist' => '信箱已被註冊過'], Response::HTTP_OK);
        }

        // 驗證碼
        $verifyNumber = rand(100000, 999999);

        // 寄信內容
        $mailContent = "您的驗證碼如下 請在5分鐘內完成輸入 驗證碼 : " . $verifyNumber;
        // 寄信
        $this->sendMail($mailContent);

        // 驗證碼加密寫入資料庫
        $this->insertVerifyNumberEncryption($mail , $verifyNumber);        
    }

    // 寄信
    public function sendMail($mailContent)
    {
        Mail::raw($mailContent ,function($message){
            $mail = $this->mail; // 使用物件寫法將信箱動態帶入
            $message->to($mail)->subject('xxxxStore註冊驗證信');
        });
    }

    // 驗證碼加密寫入資料庫
    public function insertVerifyNumberEncryption($mail , $verifyNumber)
    {
        // 信箱、驗證碼加密
        $encryptionString = $mail . $verifyNumber;
        $verifyNumberEncryption = md5($encryptionString);

        // 驗證碼資訊寫入資料庫
        $userMailVerify['mail'] = $mail;
        $userMailVerify['verifyNumberEncryption'] = $verifyNumberEncryption;
        $startTime =  date("Y-m-d H:i:s");
        $fiveMinute = 60*5; // 設定過期時間為5分鐘
        $userMailVerify['experTime'] = date("Y-m-d H:i:s", strtotime($startTime) + $fiveMinute);        

        // 寫入信箱驗證碼
        UserMailVerifyModel::insert_user_mail_verify_db($userMailVerify);
    }

    // 刪除已完成註冊之信箱
    public function deleteVerifyedMail($mail)
    {
        UserMailVerifyModel::delete_user_mail_verify_where_mail_db($mail);
    }
}
