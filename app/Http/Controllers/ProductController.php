<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::orderBy('id', 'desc')->get();
        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     * @param App\Http\Requests\StoreProductRequest $request
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'image_path' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'type_id' => 'required|exists:types,id',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        $data = $request->only(['name', 'type_id']);
        $data['image_path'] = $request->file('image_path')->store('products', 'public');
        $product = Product::create($data);
        return response()->json([
            'product' => $product
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'image_path' => $request->hasFile('image_path') ? 'image|mimes:jpeg,png,jpg,gif,svg|max:2048' : '',
            'type_id' => 'required|exists:types,id',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        $data = $request->only(['name', 'type_id']);
        // Si nos mandan una nueva imagen
        if ($request->hasFile('image_path')) {
            // Checamos si existe ya una imagen el producto y en el disco y la eliminamos
            if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
                Storage::disk('public')->delete($product->image_path);
            }
            // Si no existe simplemente guardamos la nueva
            $data['image_path'] = $request->file('image_path')->store('products', 'public');
        } else {
            // Si no nos mandan una nueva imagen, simplemente guardamos la que ya tenia
            $data['image_path'] = $product->image_path;
        }
        $product->update($data);
        return response()->json([
            'product' => $product
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Si existe una imagen la eliminamos
        if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
            Storage::disk('public')->delete($product->image_path);
        }
        $product->delete();
        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }
}
