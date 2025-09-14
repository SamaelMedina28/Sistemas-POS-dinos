<?php

namespace App\Http\Controllers;

use App\Models\Cut;
use Illuminate\Http\Request;

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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Cut $cut)
    {
        //
    }
}
