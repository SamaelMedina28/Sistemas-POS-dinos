<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Contracts\Providers\JWT;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    // Registrar un usuario
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            // 'password_confirmation' => 'required|string|min:8',
        ]);

        // Si la validación falla, retorna errores
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Crear el usuario si la validación pasa
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Retornar el usuario creado
        return response()->json(['user' => $user], 201);
    }

    // Login de un usuario
    public function login(Request $request)
    {
        // Validar los datos de entrada
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        // Si la validación falla, retorna errores
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Extraer unicamente email y password del request
        $credentials = $request->only('email', 'password');

        try {
            // Si las credenciales son correctas genera un token y si no retorna error
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }
            // Si todo va bien, retorna el usuario y el token en forma de cookie
            return response()->json(['user' => JWTAuth::user()], 200)->cookie('token', $token, 60 * 24); // Cookie por 1 día
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token', 'exception' => $e->getMessage()], 500);
        }
    }
}
