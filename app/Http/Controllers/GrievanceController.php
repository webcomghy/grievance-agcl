<?php

namespace App\Http\Controllers;

use App\Models\Grievance;
use App\Models\GrievanceTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GrievanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $grievances = Grievance::query()
                ->select('id', 'consumer_no', 'ca_no', 'ticket_number', 'category', 'name', 'phone', 'priority_score', 'status', 'created_at')
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

        if (auth()->guard('consumer')->check()) {
            return view('consumer.form', compact('categories'));
        }

        if(auth()->user() === null){
            return view('guest.form', compact('categories'));
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

        if ($validatedData['is_grid_admin'] == 1) {
            $user = Auth::user()->username;
            $validatedData['grid_user'] = $user;
        }

        DB::beginTransaction();
        try {
            $category_priority = Grievance::$categories_priority[$validatedData['category']];

            $validatedData['priority_score'] = $category_priority;
            $validatedData['status'] = Grievance::$statuses[0];

            $grievance = Grievance::create($validatedData);

            $grievance_id = $grievance->id;
            $date = date('ymd');
            $time = date('His');

            $raw_string = $grievance_id . $date . $time;

            $encoded_ticket = $this->encode((int) $raw_string);

            $ticket_number = 'TKT-' . $encoded_ticket;

            $grievance->update(['ticket_number' => $ticket_number]);


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

        $users = User::select('id', 'username')->get();
        return view('grievance.show', compact('grievance', 'users'));
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
        $validatedData = $request->validate([
            'status' => 'required|in:' . implode(',', Grievance::$statuses),
            'description' => 'required|string',
        ]);

        DB::beginTransaction();
        try {

            $grievance->update([
                'status' => $validatedData['status'],
            ]);

            $grievance->transactions()->create([
                'status' => $validatedData['status'],
                'description' => $validatedData['description'],
                'assigned_to' => $request->assigned_to ?? 0,
                'employee_id' => $request->employee_id ?? 0,
                'created_by' => auth()->id(),
            ]);

            if ($validatedData['status'] === 'Resolved' || $grievance->status === 'Closed') {
                $grievance->update(['priority_score' => 0]);
            }

            DB::commit();
            return redirect()->route('grievances.show', $grievance)
                ->with('success', 'Grievance updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            // dd($e);
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

    public function outbox()
    {
        if (request()->ajax()) {
            $grievances = Grievance::whereHas('transactions', function ($query) {
                $query->where('created_by', auth()->id());
            })
            ->select('id', 'consumer_no', 'ca_no', 'ticket_number', 'category', 'name', 'phone', 'priority_score', 'status', 'created_at')
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
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('grievance.index', ['isOutbox' => true]);
    }

    public function inbox()
    {
        if (request()->ajax()) {
            $grievances = Grievance::whereHas('transactions', function ($query) {
                    $query->where('assigned_to', auth()->id()); 
                })
                ->select('id', 'consumer_no', 'ca_no', 'ticket_number', 'category', 'name', 'phone', 'priority_score', 'status', 'created_at')
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
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('grievance.index', ['isInbox' => true]);
    }

    public function decodeTicket($ticket_number)
    {
        $encoded_ticket = str_replace('TKT-', '', $ticket_number);

        $decoded_number = $this->decode($encoded_ticket);

        $decoded_string = str_pad($decoded_number, 14, '0', STR_PAD_LEFT);
        $grievance_id = substr($decoded_string, 0, -12);
        $date = substr($decoded_string, -12, 6);
        $time = substr($decoded_string, -6);

        return response()->json([
            'grievance_id' => $grievance_id,
            'date' => $date,
            'time' => $time,
        ]);
    }

    private function encode($number)
    {
        $charset = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $base = strlen($charset);
        $encoded = '';

        while ($number > 0) {
            $remainder = $number % $base;
            $encoded = $charset[$remainder] . $encoded;
            $number = floor($number / $base);
        }

        return $encoded;
    }


    /**
     * Decode a Base62 encoded string back to a number.
     */
    private function decode($string)
    {
        $charset = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $base = strlen($charset);
        $decoded = 0;
        $length = strlen($string);

        for ($i = 0; $i < $length; $i++) {
            $decoded = $decoded * $base + strpos($charset, $string[$i]);
        }

        return $decoded;
    }
}
