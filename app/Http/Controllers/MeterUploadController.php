<?php

namespace App\Http\Controllers;

use App\Models\AvailabilityDate;
use App\Models\MeterUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MeterUploadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    
    public function index()
    {
        if (request()->ajax()) {
            $data = MeterUpload::query()
                ->select('id', 'meter_no', 'consumer_no', 'phone_number', 'yearMonth', 'reading', 'image', 'latitude', 'longitude', 'created_at')
                ->orderBy('id', 'desc');
            return datatables()->of($data)->make(true);
        }
 
        return view('meter_uploads.index');
    }


    public function setMonthAndDate(){
        
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

        if(request()->ajax()) {
            $data = AvailabilityDate::query()
                ->select('month', 'year', 'from_date', 'to_date')
                ->orderBy('month', 'desc')
                ->orderBy('year', 'desc');
            return datatables()->of($data)->make(true);
        }

        return view('meter_uploads.set_month_and_date', compact('months', 'years', ));
    }


    public function storeMonthDates(Request $request){

        
        $request->validate([
            'month' => 'required',
            'year' => 'required',
            'from_date' => 'required',
            'to_date' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $month = $request->month;
            $year = $request->year;
            $from_date = $request->from_date;
            $to_date = $request->to_date;

            AvailabilityDate::create([
                'month' => $month,
                'year' => $year,
                'from_date' => $from_date,
                'to_date' => $to_date,
            ]);
            DB::commit();
            return redirect()->route('meter_uploads.set_dates')->with('success', 'Availability Dates Set Successfully');
    }
    catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', "Failed to set Availability Dates");
    }
    }
}
