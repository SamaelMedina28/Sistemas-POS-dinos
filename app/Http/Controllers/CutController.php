<?php

namespace App\Http\Controllers;

use App\Models\Cut;
use App\Models\Lot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Cut::orderByDesc('id')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:x,z',
            'cash' => 'required',
            'card' => 'required',
        ], [
            'type.required' => 'El tipo es requerido',
            'type.in' => 'El tipo debe ser x o z',
            'cash.required' => 'El efectivo es requerido',
            'card.required' => 'La tarjeta es requerida',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $lot = Lot::latest()->first();
        return $lot;

        if ($request->type == 'x') {
            $cut = Cut::create([
                'type' => $request->type,
                'date' => now(),
                'time' => now(),
                'product_count' => $lot->product_count,
                'lot_id' => $lot->id,
            ]);
            $cut->cutDetails()->create([
                'cash' => $request->cash,
                'card' => $request->card,

            ]);
        }
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Cut $cut)
    {
        //
    }
}
