<?php

namespace App\Http\Controllers;

use App\Models\Grievance;
use Illuminate\Http\Request;
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
                ->select('id', 'consumer_no', 'ca_no', 'category', 'name', 'phone', 'priority_score', 'status', 'created_at')
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

        $user = auth()->user();

        if ($user == null) {
            return view('grievance.form', compact('categories'));
        }


        return view('grievance.formgrid', compact('categories'));
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
        $validatedData = $request->validate([
            'consumer_no' => 'required_if:ca_no,null',
            'ca_no' => 'required_if:consumer_no,null',
            'category' => 'required',
            'name' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'description' => 'required',
            'is_grid_admin' => 'required|boolean',
        ]);


        // Perform sentiment analysis
        $analyzer = new Analyzer();
        $result = $analyzer->getSentiment($validatedData['description']);

        // dump($result);
        // Calculate priority (1-10 scale)
        $normalizedScore = ($result['compound'] + 1) / 2; // Convert -1 to 1 range to 0 to 1
        $description_priority = round($normalizedScore * 9) + 1; // Convert to 1-10 scale

        // dump($description_priority);

        $category_priority = Grievance::$categories_priority[$validatedData['category']];

        // priority is 10% description + 90% category
        $priority = round(($description_priority * 0.1) + ($category_priority * 0.9));



        // dump($priority);
        // Add priority to validated data
        $validatedData['priority_score'] = $priority;
        $validatedData['status'] = Grievance::$statuses[0];

        // dd($validatedData);
        // Create the grievance
        $grievance = Grievance::create($validatedData);

        return redirect()->route('grievances.index')->with('success', 'Grievance created successfully.');
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
