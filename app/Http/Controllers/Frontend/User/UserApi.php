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
        $accessToken = $request->accessToken;

        // token解析 轉入middleware
        $tokenCheck = JwtTrait::jwtDecode($accessToken);
        
        if($tokenCheck){
            $userId =  $tokenCheck['uid'];
        }else{
            return false;
        }
        // token解析 轉入middleware


        // 抓資料
        $userBasicData = UserModel::select_user_userName_userImage_db($userId);

        // 回傳
        if($userBasicData){
            return response()->json(['userBasicData' => $userBasicData[0]], Response::HTTP_OK);
        }else{
            return response()->json(['userBasicData' => null], Response::HTTP_OK);
        }
    }

    // jwt 改到trait

    // // jwtEncode
    // public function jwtEncode($uid)
    // {       
    //     $header = [
    //         'typ'=> 'JWT',
    //         'alg'=> 'HS256',
    //     ];

    //     // token核發時間
    //     $iat = time();
    //     // token過期時間 預設3小時(變成秒數) 3 * 60 * 60
    //     $exp = time() + 10800;

    //     $payload = [
    //         'uid' => $uid,
    //         'iat' => $iat,
    //         'exp' => $exp
    //     ];

    //     $header_payload = base64_encode(json_encode($header)) . "." . base64_encode(json_encode($payload));

    //     $secretKey = env('JWT_SECRET_KEY');
    //     $signature = hash_hmac('sha256', $header_payload, $secretKey);

    //     $accesstoken = $header_payload . "." . base64_encode($signature);

    //     return $accesstoken;
    // }

    // // jwtDecode
    // public function jwtDecode($accessToken)
    // {
    //     // 將accessToken分解 如果不是三個部分 false
    //     $tokens = explode('.', $accessToken);
    //     if (count($tokens) != 3){
    //         return false;
    //     }

    //     // 用list將要解析的三個部分拆分
    //     list($base64header, $base64payload, $signature) = $tokens;       

    //     // 取得jwt算法 如果沒有alg false
    //     $base64decodeheader = json_decode(base64_decode($base64header),true);
    //     if (empty($base64decodeheader['alg'])){
    //         return false;
    //     }
 
    //     // $signature 簽章驗證
    //     $header_payload = $base64header . '.' . $base64payload;
    //     if ($this->signatureCheck($base64decodeheader['alg'] , $header_payload) !== $signature){
    //         return false;
    //     }         

    //     $payload = json_decode(base64_decode($base64payload), true);

    //     // 簽證核發時間大於當前時間 代表簽證是錯的 false
    //     if(isset($payload['exp']) && $payload['iat'] > time()){
    //         return false;
    //     }

    //     // 簽證過期時間小於當前時間 代表簽證已過期 false
    //     if(isset($payload['exp']) && $payload['exp'] < time()){
    //         return false;
    //     }

    //     return $payload;
    // }

    // // 簽章驗證
    // public function signatureCheck($alg , $header_payload)
    // {
    //     $secretKey = env('JWT_SECRET_KEY');

    //     $alg_config = [
    //         'HS256'=>'sha256'
    //     ];            

    //     return base64_encode(hash_hmac($alg_config[$alg] , $header_payload , $secretKey));
    // }
}
