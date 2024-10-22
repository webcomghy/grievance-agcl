<?php

namespace App\Http\Controllers;

use App\Models\Grievance;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Grievance::query()->where('status', 'Pending');

            // Filter based on time since created
            if ($request->has('time_filter')) {
                $timeFilter = $request->input('time_filter');
                $now = now();

                if ($timeFilter == '24_hours') {
                    $query->where('created_at', '>=', $now->subHours(24));
                } elseif ($timeFilter == '48_hours') {
                    $query->where('created_at', '<', $now->subHours(24))
                          ->where('created_at', '>=', $now->subHours(48));
                } elseif ($timeFilter == 'above_48') {
                    $query->where('created_at', '<', $now->subHours(48));
                }
            }

            return datatables()->of($query)
                ->addColumn('actions', function ($row) {
                    return '<button class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-1 px-2 rounded">View</button>';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('reports.index');
    }

}
