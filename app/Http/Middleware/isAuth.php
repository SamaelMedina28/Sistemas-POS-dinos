<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class isAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Checamos si hay una cookie llamada 'token'
       $token = $request->cookie('token');

        if ($token) {
            // Checa si ese token es válido para algun usuario
            $user = JWTAuth::setToken($token)->authenticate();
            if ($user) {
                return $next($request);
            }
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
