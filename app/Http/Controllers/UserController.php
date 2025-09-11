<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('id', '!=', JWTAuth::user()->id)->orderBy('id', 'desc')->get();
        return response()->json([
            'users' => $users
        ], 200);
    }

    // Crear un usuario
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:admin,sales',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return response()->json([
            'user' => $user
        ], 201);
    }

    // Actualizar un usuario
    // TODO: Proteger que un usuario admin no pueda bajar de rango a otro admin
    public function update(Request $request, User $user)
    {
        if ($user->id == JWTAuth::user()->id) {
            return response()->json([
                'error' => 'You cannot update yourself'
            ], 403);
        }
        // Verificar si el usuario es un admin y el usuario autenticado no es el root
        if($user->role == 'admin' && JWTAuth::user()->role != 'root') {
            return response()->json([
                'error' => 'Only the root user can update an admin'
            ], 403);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:admin,sales',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return response()->json([
            'user' => $user
        ], 200);
    }

    // Eliminar un usuario
    public function destroy(User $user)
    {
        // Verificar que el usuario no sea el mismo que esta autenticado
        if ($user->id == JWTAuth::user()->id) {
            return response()->json([
                'error' => 'You cannot delete yourself'
            ], 403);
        }
        // Verificar que el usuario no sea un admin (solo el root puede eliminar a un admin)
        if($user->role == 'admin' && JWTAuth::user()->role != 'root') {
            return response()->json([
                'error' => 'Only the root user can delete an admin'
            ], 403);
        }
        $user->delete();
        return response()->json([
            'message' => 'User deleted successfully'
        ], 200);
    }
}
