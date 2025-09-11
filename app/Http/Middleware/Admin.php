<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->cookie('token');
        try {
            if ($token) {
                // Checa si ese token es válido para algun usuario
                $user = JWTAuth::setToken($token)->authenticate();
                if ($user) {
                    if ($user->role !== 'admin') {
                        return response()->json(['error' => 'Unauthorized', 'role' => $user->role], 401);
                    }
                    return $next($request);
                }
            }
        } catch (\Throwable $th) {
            // Si el token es inválido, retornamos error y borramos la cookie
            return response()->json(['error' => 'Unauthorized', 'message' => $th->getMessage()], 401)
                ->cookie('token', '', -1); // Cookie vacía con tiempo de expiración en el pasado
        }
        return $next($request);
    }
}
