<?php

namespace App\Http\Controllers\Frontend\User;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class UserApi extends Controller
{
    protected $mail;

    // 寄驗證碼信件
    public function sendVerifyMail(Request $request)
    {
        // 信箱存入物件 以便寄信function可以動態改變信箱
        $mail = $request->mail;
        $this->mail = $mail;

        // 判斷信箱是否已註冊過

        // 驗證碼
        $verifyNumber = rand(100000, 999999);

        // 寄信內容
        $mailContent = "您的驗證碼如下 請在5分鐘內完成輸入 驗證碼 : " . $verifyNumber;
        // 寄信
        $this->sendMail($mailContent);

        // 信箱、驗證碼加密
        $encryptionString = $mail . $verifyNumber;
        $encryption = md5($encryptionString);

        //剛剛加密的存入session 時間設定五分鐘 
        $request->session()->put('verifyNumberEncryption', $encryption);
        // session(['verifyNumberEncryption' => $encryption]);
        // session::save();

        $verifyNumberEncryption = $request->session()->get('verifyNumberEncryption');
        return Session::all();
        return $verifyNumberEncryption;
    }

    // 寄信
    public function sendMail($mailContent)
    {
        // $mail = '0451008@nkust.edu.tw';
        Mail::raw($mailContent ,function($message){
            $mail = $this->mail; // 使用物件寫法將信箱動態帶入
            $message->to($mail)->subject('xxxxStore註冊驗證信');
        });
    }

    // 註冊
    public function signup(Request $request)
    {
        $verifyNumberEncryption = $request->session()->get('verifyNumberEncryption');
        $verifyNumberEncryption =  Session::get('verifyNumberEncryption');
        return Session::all();
        return $verifyNumberEncryption;



        $mail = $request->mail;
        $verifyNumber = $request->verifyNumber;
        $userName = $request->userName;
        $password = $request->password;
        
        // 驗證信箱是否正確
        $verifyString = $mail . $verifyNumber;
        $mailCheck = $this->mailCheck($verifyString);
        if(!$mailCheck){
            return response()->json(['mailCheck' => '信箱錯誤'], Response::HTTP_OK);
        }




    }

    // 驗證信箱是否正確
    public function mailCheck($verifyString)
    {
        $verifyNumberEncryption = Session::get('verifyNumberEncryption');
        $verifyNumberCheck = md5($verifyString);
        if($verifyNumberCheck === $verifyNumberEncryption){
            return true;
        }else{
            return false;
        }
    }
}
