<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AdminModel;
use App\Http\Traits\ToolTrait;
use Illuminate\Support\Facades\Cookie;

class AdminController extends Controller
{
    use ToolTrait;
    //建構子 middleware設定
    public function __construct()
    {
        $this->middleware('authCheck')->except('login','generateToken');
    }
    
    /*********************************************view************************************************ */
    //首頁畫面
    public function index()
    {  
        return view('backstage.index'); //有帳號無token 代表帳號正卻，產生新token 存入資料庫跟session
    }
    /*********************************************view************************************************ */
    
    

    /********************************************* 登入驗證 Encryption加密************************************************ */
    // 登入驗證 Encryption加密
    public function login(Request $request)
    {
        $account = $request->account; //帳號
        $password = $request->password;   //密碼
        // return password_hash($password, PASSWORD_DEFAULT);   //密碼加密方式

        $passwordEncryption = AdminModel::select_admin_password_db($account);  //取得資料庫加密過後密碼
        if(empty($passwordEncryption)){
            $this->talk('帳號或密碼有誤!', route('view.backStage.adminLogin'), 3); //跳轉回登入頁面
        }
        
        $currentPassword = $passwordEncryption[0]->password;
        if(password_verify($password,$currentPassword)){    //驗證密碼是否正確 正確的話存token 不正確跳回登入頁面
            //帳號密碼正確，刷新token 並存管理員資料入session
            $newToken = $this->generateToken($account); //產生新token
            AdminModel::update_admin_token_db($account,$currentPassword,$newToken);    //更新token

            $admin = AdminModel::select_admin_data_db($account,$currentPassword);    //取得admin資料

            $accessToken = $admin[0]->accessToken; 
            $adminName = $admin[0]->adminName;
            $adminId = $admin[0]->adminId;

            //資料存session
            session(['accessToken' => $accessToken]);    //新token存入session
            session(['adminName' => $adminName]);    //adminName存入session
            session(['adminId' => $adminId]);    //userId存入session

            //資料存cookie
            Cookie::queue('accessToken', $accessToken, 1800);
            Cookie::queue('adminName', $adminName, 1800);
            Cookie::queue('adminId', $adminId, 1800);

            $this->talk('',route('adimnIndex'),2);   //跳至首頁
        } else {    //不正確跳回登入頁面
            $this->talk('帳號或密碼有誤!',route('view.backStage.adminLogin'),3);   //跳轉回登入頁面
        }
    }

    //產生新token Encryption加密
    public function generateToken($account)
    {
        $randomString = $this->randomString(32);
        $tokenUnEncryption = time() . $account . $randomString;
        $token = md5($tokenUnEncryption);
        return $token;
    }
    /********************************************* 登入驗證 Encryption加密************************************************ */    

    //登出 
    public function logout()
    {
        session_unset();    //清除session
        $this->talk('',route('view.backStage.adminLogin'),2);   //跳轉回登入頁面
    }
}
