<?php

namespace App\Http\Controllers;

use App\Imports\SelfReadingImport;
use App\Models\AvailabilityDate;
use App\Models\ConsumerMaster;
use App\Models\MeterfailedLog;
use App\Models\MeterUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class MeterUploadController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $username = Auth::user() ? Auth::user()->username : NULL;

        // dd($username);

        $mobileNumber = session('mobile_number');

        // dd($mobileNumber);
        $isMobileNumber = false;
        if($mobileNumber){
            $isMobileNumber = true;
        }

        $isConsumer = auth()->guard('consumer')->check();
        $consumerNo = auth()->guard('consumer')->user()->consumer_number ?? NULL;

        $consumerMaster = ConsumerMaster::select('CONSUMER_NO', 'BA_NO', 'CA_NO')
            ->firstWhere('CONSUMER_NO', $consumerNo);


        if (request()->ajax()) {
            $data = MeterUpload::query()
                ->select(
                    'id', 
                    'meter_no', 
                    'consumer_no', 
                    'phone_number',
                    'yearMonth', 
                    'reading', 
                    'image', 
                    'latitude', 
                    'longitude', 
                    'created_at'
                );

            if($isMobileNumber) {
                $data->where('phone_number', $mobileNumber);
            }

            if ($username !== 'admin' && $isConsumer === false && $isMobileNumber === false) {
                $data->where('grid_id', $username);
            }

            if($isConsumer) {
                // $data->where('consumer_no1', $consumerMaster->CONSUMER_NO);
                $data->where(function($query) use ($consumerMaster) {
                    $query->where('consumer_no', $consumerMaster->CA_NO)
                          ->orWhere('consumer_no', $consumerMaster->BA_NO)
                          ->orWhere('consumer_no', $consumerMaster->CONSUMER_NO);
                });
            }

            $data->orderBy('id', 'desc');
            return datatables()->of($data)->make(true);
        }

        if($isConsumer || $isMobileNumber) {
            return view('consumer.meter_uplods.index');
        }

        return view('meter_uploads.index');
    }


    public function setMonthAndDate()
    {
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

        if (request()->ajax()) {
            $data = AvailabilityDate::query()
                ->select('id', 'month', 'year', 'from_date', 'to_date')
                ->orderBy('month', 'desc')
                ->orderBy('year', 'desc');

            return datatables()->of($data)
                ->addColumn('actions', function ($row) {
                    return '<a href="' . route('meter_uploads.edit', $row->id) . '" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-1 px-2 rounded-md">Edit</a>';
                })
                ->rawColumns(['actions']) // Allow HTML in the actions column
                ->make(true);
        }

        return view('meter_uploads.set_month_and_date', compact('months', 'years'));
    }


    public function storeMonthDates(Request $request)
    {
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
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', "Failed to set Availability Dates");
        }
    }

    public function editMonthDate($id)
    {
        $availabilityDate = AvailabilityDate::findOrFail($id);
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

        return view('meter_uploads.edit_month_date', compact('availabilityDate', 'months', 'years'));
    }

    public function updateMonthDate(Request $request, $id)
    {
        $request->validate([
            'month' => 'required',
            'year' => 'required',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);

        $availabilityDate = AvailabilityDate::findOrFail($id);
        $availabilityDate->update($request->all());

        return redirect()->route('meter_uploads.set_dates')->with('success', 'Availability Date Updated Successfully');
    }

    public function showUploadForm()
    {
        return view('meter_uploads.import'); // Create this view
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        Excel::import(new SelfReadingImport, $request->file('file'));

        return redirect()->back()->with('success', 'Data imported successfully.');
    }

    public function failedUploads(Request $request)
    {
        if ($request->ajax()) {
            $data = MeterfailedLog::query()
                ->select(
                    'id', 
                    'consumer_no', 
                    'phone_number', 
                    'yearMonth', 
                    'previousReading', 
                    'reading', 
                    'meter_no', 
                    'latitude', 
                    'longitude',  
                    'created_at'
                )->orderBy('id', 'desc');; // Adjust the fields as necessary

            return datatables()->of($data)
                ->rawColumns(['actions']) // Allow HTML in the actions column
                ->make(true);
        }

        return view('meter_uploads.failed_uploads'); // Create this view
    }
}
