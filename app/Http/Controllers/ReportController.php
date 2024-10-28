<?php

namespace App\Http\Controllers;

use App\Models\Grievance;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request, $status = null)
    {
        if ($request->ajax()) {
            $query = Grievance::select(
                    'id', 
                    'consumer_no', 
                    'ca_no', 
                    'ticket_number', 
                    'category', 
                    'subcategory',
                    'name', 
                    'phone', 
                    'priority_score',
                    'grid_code', 
                    'status', 
                    'created_at'
                )->where('status', $status);

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

            if ($request->has('category')) {
                $query->where('category', $request->input('category'));
            }

            return datatables()->of($query)
                
                ->addColumn('priority', function ($row) {
                    return $row->priority; 
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('reports.index' , ['status' => $status]);
    }

}
