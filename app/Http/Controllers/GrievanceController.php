<?php

namespace App\Http\Controllers;

use App\Models\Grievance;
use Illuminate\Http\Request;

class GrievanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $data = Grievance::query();
            return datatables()->of($data)->make(true);
        }
        return view('grievance.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Grievance::$categories;

        return view('grievance.form', compact('categories'));
    }

    public function sendOtp(Request $request)
{
    $mobile = $request->input('mobile');
    // Logic to send OTP to the mobile number
    // For demonstration, assume OTP is always '1234'
    return response()->json(['success' => true]);
}

public function verifyOtp(Request $request)
{
    $otp = $request->input('otp');
    // Replace with actual OTP verification logic
    if ($otp === '1234') {
        return response()->json(['success' => true]);
    } else {
        return response()->json(['success' => false]);
    }
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        dd($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(Grievance $grievance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Grievance $grievance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Grievance $grievance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Grievance $grievance)
    {
        //
    }
}
