<?php

namespace App\Http\Controllers\Frontend\User;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\UserModel;
use App\Models\UserMailVerifyModel;

use App\Http\Controllers\Frontend\User\UserMailVerifyApi;

use App\Http\Traits\ToolTrait;


class UserApi extends Controller
{   use ToolTrait;
    //建構子 middleware設定
    public function __construct()
    {

    }

    // 註冊
    public function signup(Request $request)
    {
        $mail = $request->mail;
        $verifyNumber = $request->verifyNumber;
        $userName = $request->userName;
        $password = $request->password;
        
        // 驗證信箱是否正確
        $mailCheck = $this->mailCheck($mail , $verifyNumber);
        if(!$mailCheck){
            return response()->json(['mailCheck' => '驗證碼錯誤'], Response::HTTP_OK);
        }

        // 使用password_hash密碼加密
        $encryptionPassword = password_hash($password, PASSWORD_DEFAULT);

        // 如果驗證正確將註冊訊息寫入資料庫
        $user['userId'] = $this->randomString(13);
        $user['userName'] = $userName;
        $user['mail'] = $mail;
        $user['password'] = $encryptionPassword;
        $accessToken = $this->randomString(23);
        $user['accessToken'] = $accessToken;
        $user['lastLoginTime'] = date('Y-m-d H:i:s');
        UserModel::insert_user_db($user);
        // 刪除已驗證完成信箱資訊
        $this->deleteVerifyedMail($mail);

        return response()->json(['accessToken' => $accessToken], Response::HTTP_OK);
    }

    // 驗證信箱是否正確
    public function mailCheck($mail , $verifyNumber)
    {
        $verifyString = $mail . $verifyNumber;
        $verifyNumberCheck = md5($verifyString);
        $nowTime = date("Y-m-d H:i:s");

        // 抓指定信箱驗證加密
        $verifyNumberEncryption = UserMailVerifyModel::select_user_mail_verify_where_mail_db($mail);
        if(!$verifyNumberEncryption){
            return false;
        }

        // 加密驗證碼
        $verifyEncryption = $verifyNumberEncryption[0]->verifyNumberEncryption;
        // 過期時間
        $experTime = $verifyNumberEncryption[0]->experTime;

        //  驗證碼相同 而且沒有超過驗證時間 回傳true
        if($verifyNumberCheck === $verifyEncryption && $nowTime <= $experTime){
            return true;
        }else{
            return false;
        }
    }

    // 刪除已驗證完成信箱資訊
    public function deleteVerifyedMail($mail)
    {
        $UserMailVerifyApi = new UserMailVerifyApi();
        $UserMailVerifyApi->deleteVerifyedMail($mail);
    }

    //  取得使用者頭像及使用者名稱
    public function getUserBasicData(Request $request)
    {
        $mail = $request->mail;

        // 抓資料
        $userBasicData = UserModel::select_user_userName_userImage_db($mail);

        // 回傳
        if($userBasicData){
            return response()->json(['userBasicData' => $userBasicData[0]], Response::HTTP_OK);
        }else{
            return response()->json(['userBasicData' => null], Response::HTTP_OK);
        }
    }
}
