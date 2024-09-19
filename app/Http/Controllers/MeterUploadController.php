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


    public function setMonthAndDate(){
        // list of month numbers name value pair
        $months = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];

        $years = range(2022, 2030);
        

        return view('meter_uploads.set_month_and_date', compact('months', 'years'));
    }


    public function storeMonthDates(Request $request){

        dd($request->all());
    }
}
