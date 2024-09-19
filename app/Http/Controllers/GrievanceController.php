<?php

namespace App\Http\Controllers;

use App\Models\Grievance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sentiment\Analyzer;

class GrievanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $grievances = Grievance::query()
                ->select('id', 'consumer_no', 'ca_no', 'ticket_number','category', 'name', 'phone', 'priority_score', 'status', 'created_at')
                ->orderByRaw("CASE WHEN status = 'Pending' THEN 0 ELSE 1 END")
                ->orderBy('priority_score', 'desc')
                ->orderBy('created_at', 'desc');

           

            return datatables()->of($grievances)
                ->addColumn('actions', function ($row) {
                    $btn = '<a href="' . route('grievances.show', $row->id) . '" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-1 px-2 rounded-md text-sm">View</a>';
                    return $btn;
                })
                ->addColumn('priority', function ($row) {
                    return $row->priority;
                })
                ->filter(function ($query) {
                    if (request()->has('priority')) {
                        $priority = request('priority');
                        $query->where(function ($q) use ($priority) {
                            if ($priority == 'High') {
                                $q->where('priority_score', '>=', 7);
                            } elseif ($priority == 'Medium') {
                                $q->whereBetween('priority_score', [4, 6]);
                            } elseif ($priority == 'Low') {
                                $q->where('priority_score', '<', 4);
                            }
                        });
                    }
                }, true)
                ->rawColumns(['actions'])
                ->make(true);
        }
        return view('grievance.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Grievance::$categories;

        // auth user

        
        if (auth()->guard('consumer')->check()) {
            // dd(auth()->guard('consumer')->user());
            return view('consumer.form', compact('categories'));
        }

        return view('grievance.form', compact('categories'));
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       
        $validatedData = $request->validate([
            'consumer_no' => 'nullable|exclude_if:category,Others,Gas Leakage|required_if:ca_no,null|',
            'ca_no' => 'nullable|exclude_if:category,Others,Gas Leakage|required_if:consumer_no,null',
            'category' => 'required',
            'name' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'description' => 'required',
            'is_grid_admin' => 'required|boolean',
            'longitude' => 'required_if:is_grid_admin,0',
            'latitude' => 'required_if:is_grid_admin,0',
        ]);

        if($validatedData['is_grid_admin'] == 1) {
            $user = Auth::user()->username;
            $validatedData['grid_user'] = $user;
        }
        
        DB::beginTransaction();
        try {
            // Use only category priority
            $category_priority = Grievance::$categories_priority[$validatedData['category']];

            // Set priority directly from category
            $validatedData['priority_score'] = $category_priority;
            $validatedData['status'] = Grievance::$statuses[0];
           
            // Create the grievance
            $grievance = Grievance::create($validatedData);

            $grievance_id = $grievance->id;
            $grievance_id = str_pad($grievance_id, 8, '0', STR_PAD_LEFT);
            $ticket_number = 'TKT-' . $grievance_id . '-' . date('Ymd') . '-' . date('His');

            $grievance->update(['ticket_number' => $ticket_number]);
            // dd("ok");
        } catch (\Throwable $th) {
            DB::rollBack();
           
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
        
        DB::commit();

        return redirect()->back()->with('success', 'Grievance created successfully. Your ticket number is: ' . $ticket_number);
    }

    /**
     * Display the specified resource.
     */
    public function show(Grievance $grievance)
    {
        $grievance->load('transactions');
        return view('grievance.show', compact('grievance'));
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
        // dd($request->all());
        $validatedData = $request->validate([
            'status' => 'required|in:' . implode(',', Grievance::$statuses),
            'description' => 'required|string',
        ]);

        DB::beginTransaction();
        try {

            $grievance->update([
                'status' => $validatedData['status'],
            ]);

            // Create a new transaction for this update
            $grievance->transactions()->create([
                'status' => $validatedData['status'],
                'description' => $validatedData['description'],
                'assigned_to' => $request->assigned_to ?? 0,
                'employee_id' => $request->employee_id ?? 0,
                'created_by' => auth()->id(),
            ]);

            // If the grievance is resolved and closed, update the priority score
            if ($validatedData['status'] === 'Resolved' || $grievance->status === 'Closed') {
                $grievance->update(['priority_score' => 0]);
            }

            DB::commit();
            return redirect()->route('grievances.show', $grievance)
                ->with('success', 'Grievance updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('grievances.show', $grievance)
                ->with('error', 'Failed to update grievance: ');
        }
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Grievance $grievance)
    {
        //
    }
}
