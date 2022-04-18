<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AdminModel;
use App\Http\Traits\ToolTrait;
// use App\Models\modelName;

class AdminController extends Controller
{
    use ToolTrait;
    //使用__construct 設定middleware
    // public function __construct()
    // {
    //     $this->middleware('auth');
    //     $this->middleware('log')->only('index');
    //     $this->middleware('subscribed')->except('store');
    // }

   //驗證帳號是否正確
   public function accountVerify(Request $request)
   {
        $account = $request->account;
        $password = $request->password;
        return hash("sha256", $password);
        return AdminModel::select_admin_db($account,$password);
   }
}
