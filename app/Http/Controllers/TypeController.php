<?php

namespace App\Http\Controllers;

use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $types = Type::orderBy('id', 'desc')->get();
        return response()->json([
            'types' => $types
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:types,name',
            'price' => 'required|numeric',
            'minutes' => 'nullable|numeric',
            'description' => 'nullable|string|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        $type = Type::create([
            'name' => $request->name,
            'price' => $request->price,
            'minutes' => $request->minutes,
            'description' => $request->description,
        ]);
        return response()->json([
            'type' => $type
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Type $type)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:types,name,' . $type->id,
            'price' => 'required|numeric',
            'minutes' => 'nullable|numeric',
            'description' => 'nullable|string|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        $type->update([
            'name' => $request->name,
            'price' => $request->price,
            'minutes' => $request->minutes,
            'description' => $request->description,
        ]);
        return response()->json([
            'type' => $type
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Type $type, Request $request)
    {
        //Si el type tiene productos y no se envia el parametro force
        if ($type->products()->count() > 0 && $request->force != true) {
            return response()->json([
                'message' => 'Type has products',
                'products' => $type->products
            ], 422);
        }
        // Si existe el parametro force, se elimina el type y sus productos
        if ($request->force == true) {
            $type->delete();
            return response()->json([
                'message' => 'Type deleted successfully'
            ], 200);
        }
        // Si no se manda el parametro pero el type no tiene productos podemos eliminar de todas formas
        $type->delete();
        return response()->json([
            'message' => 'Type deleted successfully'
        ], 200);
    }
}
