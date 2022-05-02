<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use App\Http\Traits\ToolTrait;


class AuthCheck
{
    use ToolTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $sessionAccessToken = Session::get('accessToken');
        $cookieAccessToken = Cookie::get('accessToken');

        if($sessionAccessToken == $cookieAccessToken){
            return $next($request);
        }else{
            $this->talk('權限錯誤',route('view.backStage.adminLogin'),3);   //跳轉回登入頁面
        }
    }
}
