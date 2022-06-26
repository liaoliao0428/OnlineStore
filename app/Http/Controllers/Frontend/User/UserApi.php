<?php

namespace App\Http\Controllers\Frontend\User;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\UserModel;
use App\Models\UserMailVerifyModel;

use App\Http\Controllers\Frontend\User\UserMailVerifyApi;

use App\Http\Traits\ToolTrait;
use App\Http\Traits\JwtTrait;


class UserApi extends Controller
{   use ToolTrait;
    //建構子 middleware設定
    public function __construct()
    {
        $this->middleware('frontAuthCheck')->except('signin','signup','mailCheck','deleteVerifyedMail');
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
        $userId = $this->randomString(13);
        $user['userId'] = $userId;
        $user['userName'] = $userName;
        $user['mail'] = $mail;
        $user['password'] = $encryptionPassword;
        $user['lastLoginTime'] = date('Y-m-d H:i:s');
        UserModel::insert_user_db($user);
        // 刪除已驗證完成信箱資訊
        $this->deleteVerifyedMail($mail);

        $accessToken = JwtTrait::jwtEncode($userId);

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

    // 登入
    public function signin(Request $request)
    {
        $mail = $request->mail;
        $password = $request->password;

        // 抓使用hash加密過的密碼
        $userData = UserModel::select_user_userId_password_where_mail_db($mail);

        if( !empty($userData) && password_verify($password, $userData[0]->password) ){
            $userId = $userData[0]->userId;
            // 密碼正確登入成功 生成新的生成jwt token 並回傳
            $signinTime = date("Y-m-d H:i:s");
            UserModel::update_user_lastLoginTime_db($userId , $signinTime);
            // 生成jwt
            $accessToken = JwtTrait::jwtEncode($userId);
            return response()->json(['signin' => true , 'accessToken' => $accessToken], Response::HTTP_OK);
        }else{
            return response()->json(['signin' => false,'message' => '信箱或密碼錯誤'], Response::HTTP_OK);
        }
    }

    //  取得使用者頭像及使用者名稱
    public function getUserBasicData(Request $request)
    {
        $userId = $request->userId;

        // 抓資料
        $userBasicData = UserModel::select_user_userName_userImage_db($userId);

        // 回傳
        if($userBasicData){
            return response()->json(['userBasicData' => $userBasicData[0]], Response::HTTP_OK);
        }else{
            return response()->json(['userBasicData' => null], Response::HTTP_OK);
        }
    }
}
