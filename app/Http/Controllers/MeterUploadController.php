<?php

namespace App\Http\Controllers;

use App\Models\MeterUpload;
use Illuminate\Http\Request;

class MeterUploadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $data = MeterUpload::query();
            return datatables()->of($data)->make(true);
        }
 
        return view('meter_uploads.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
    public function show(MeterUpload $meterUpload)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MeterUpload $meterUpload)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MeterUpload $meterUpload)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MeterUpload $meterUpload)
    {
        //
    }
}
