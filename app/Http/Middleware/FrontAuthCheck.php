<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
// use Illuminate\Http\Response;
use App\Http\Traits\JwtTrait;

class FrontAuthCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // token解析 
        $accessToken = $request->accessToken;
        $tokenCheck = JwtTrait::jwtDecode($accessToken);
        // 如果tokenCheck驗證成功 取出uid 並前往下一步
        if($tokenCheck){
            $userId =  $tokenCheck['uid'];
            $request->userId = $userId;
            return $next($request);
        }else{
            return response()->json([false]);
        }
    }
}
