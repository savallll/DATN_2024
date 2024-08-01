<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redirect;
use Tymon\JWTAuth\Facades\JWTAuth as Auth;
use Symfony\Component\HttpFoundation\Response;

class jwtAuth
{
    public function handle($request, Closure $next)
    {
        try {
            // Lấy token từ cookie
            $token = Cookie::get('jwt_token');
            if (!$token) {
                return Redirect::route('auth');
            }

            // Xác thực token
            Auth::setToken($token);
            $user = Auth::authenticate();
            if (!$user) {
                return Redirect::route('auth');
            }
        } catch (Exception $e) {
            return Redirect::route('auth');
        }

        // return route('auth');
        return $next($request);

    }
}
