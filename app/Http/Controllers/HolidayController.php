<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\HolidaysImport;

class HolidayController extends Controller
{
    public function showUploadForm()
    {
        $holidays = Holiday::all();
        return view('admin.upload_holiday', compact('holidays'));
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            Excel::import(new HolidaysImport, $request->file('file'));
            return redirect()->back()->with('success', 'Holidays imported successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to import holidays: ' . $e->getMessage());
        }
    }

    
}
