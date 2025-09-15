<?php

namespace App\Http\Controllers;

use App\Models\Lot;
use Illuminate\Http\Request;

class LotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Filtro por fecha
        $lots = Lot::when($request->start_date, function ($query) use ($request) {
            return $query->where('date', '>=', $request->start_date);
        })->when($request->end_date, function ($query) use ($request) {
            return $query->where('date', '<=', $request->end_date);
        })->with('cuts.cutDetail')->get();
        // Traer todos los lotes
        return response()->json([
            'lots' => $lots
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Lot $lot)
    {
        return response()->json([
            'lot' => $lot->load('cuts.cutDetail','sales.products.type','sales.payment')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lot $lot)
    {
        $lot->delete();
        return response()->json([
            'message' => 'Lote eliminado correctamente'
        ]);
    }
}
