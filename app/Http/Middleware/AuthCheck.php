<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
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
        $cookieAccessToken = Cookie::get('accessToken');
        $sessionAccessToken = session('accessToken', 'default');
        if($cookieAccessToken != $sessionAccessToken){
            $this->talk('權限錯誤',route('view.backStage.adminLogin'),3);   //跳轉回登入頁面
        }
        return $next($request);
    }
}
