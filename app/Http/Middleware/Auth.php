<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class Auth
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

        if (!$token) {
            return response()->json(['error' => 'Token not provided'], 401);
        }
        try {
            $user = JWTAuth::setToken($token)->authenticate();

            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            return $next($request);
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'Token expired'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Token invalid', 'message' => $e->getMessage(), 'token' => $token], 401);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token error', 'message' => $e->getMessage()], 401);
        }
    }
}
