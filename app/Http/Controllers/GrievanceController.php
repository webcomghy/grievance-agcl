<?php

namespace App\Http\Controllers;

use App\Models\ConsumerMaster;
use App\Models\Grievance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class GrievanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {

        
        $isConsumer = auth()->guard('consumer')->check();
        $consumerID = auth()->guard('consumer')->user()->id ?? NULL;
        
        $grid_code = Auth::user()->grid_code;

        $isAdmin = Auth::user()->hasRole('admin');
        $isCallCenter = Auth::user()->hasRole('support');
        $isNodalOfficer = Auth::user()->hasRole('nodal_officer');

        
        if (request()->ajax()) {
            $grievances = Grievance::query()
                ->select(
                        'id', 
                        'consumer_no', 
                        'ca_no', 
                        'ticket_number', 
                        'category', 
                        'name', 
                        'phone', 
                        'priority_score', 
                        'status', 
                        'created_at'
                    )
                ->when($isConsumer, function ($query) use ($consumerID) {
                    return $query->where('consumer_id', $consumerID);
                })
                ->when(!$isConsumer && !$isAdmin && !$isCallCenter && !$isNodalOfficer, function ($query) use ($grid_code) {
                    return $query->where('grid_code', $grid_code);
                })
                ->orderByRaw("CASE WHEN status = 'Pending' THEN 0 ELSE 1 END")
                ->orderBy('priority_score', 'desc')
                ->orderBy('created_at', 'desc');

            return datatables()->of($grievances)
                ->addColumn('actions', function ($row) use ($isConsumer) {
                    $encryptedId = Crypt::encryptString($row->id);
                    $btnRoute = $isConsumer ? 'grievances.showuser' : 'grievances.show';
                    $btn = '<a href="' . route($btnRoute, $encryptedId) . '" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-1 px-2 rounded-md text-sm">View</a>';
                    return $btn;
                })
                ->addColumn('priority', function ($row) {
                    return $row->priority;
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        if($isConsumer){
            return view('consumer.grievance.index');
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

        if(Auth::user() === null){
            return view('guest.form', compact('categories'));
        }

        return view('grievance.form', compact('categories'));
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all()); // Removed for processing

    $validatedData = $request->validate([
            'consumer_no' => 'nullable|exclude_if:category,Others,Gas Leakage|required_if:ca_no,null|',
            'ca_no' => 'nullable|exclude_if:category,Others,Gas Leakage|required_if:consumer_no,null',
            'category' => 'required',
            'subcategory' => 'required',
            'name' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'description' => 'required',
            'admin_remark' => 'nullable',
            'file_upload' => 'nullable|file|max:2048|mimes:jpg,jpeg,png,pdf', // Validate file types
            'is_grid_admin' => 'required|boolean',
            'longitude' => 'required_if:is_grid_admin,0',
            'latitude' => 'required_if:is_grid_admin,0',
        ], [
            'consumer_no.required_if' => 'Consumer number is required when CA number is not provided.',
            'ca_no.required_if' => 'CA number is required when Consumer number is not provided.',
            'category.required' => 'Category is required.',
            'name.required' => 'Name is required.',
            'address.required' => 'Address is required.',
            'phone.required' => 'Phone number is required.',
            'description.required' => 'Description is required.',
            'is_grid_admin.required' => 'Grid admin status is required.',
            'longitude.required_if' => 'Longitude is required if not a grid admin.',
            'latitude.required_if' => 'Latitude is required if not a grid admin.',
        ]);

        // Check for double extensions
        if ($request->hasFile('file_upload')) {
            $file = $request->file('file_upload');
            if ($this->hasDoubleExtension($file->getClientOriginalName())) {
                return redirect()->back()->with('error', 'File name contains a double extension.');
            }
        }

        if ($validatedData['is_grid_admin'] == 1) {
            $user = Auth::user()->id;
            $validatedData['grid_user'] = $user;
        } else {
            $validatedData['grid_user'] = null;
            $validatedData['consumer_id'] = auth()->guard('consumer')->user()->id ?? null;
        }

        if($validatedData['ca_no'] === NULL){
            $ca_number = ConsumerMaster::where('CONSUMER_NO', $validatedData['consumer_no'])->first()->CA_NO ?? null;
            if($ca_number === null){
                return redirect()->back()->with('error', 'Consumer not found.');
            }
            $validatedData['grid_code'] = substr($ca_number, 2, 4); // Exclude first two characters and take next four
        }else{
            $validatedData['grid_code'] = substr($validatedData['ca_no'], 2, 4);
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

            // Handle file upload
            if ($request->hasFile('file_upload')) {
                $file = $request->file('file_upload');
                $ticket_number = 'TKT-' . $encoded_ticket; // Ensure ticket number is generated

                // Create a directory for the ticket number if it doesn't exist
                $directoryPath = public_path('uploads/' . $ticket_number);
                if (!file_exists($directoryPath)) {
                    mkdir($directoryPath, 0755, true);
                }

                // Store the file in the created directory
                $filePath = $file->move($directoryPath, $file->getClientOriginalName()); // Use move instead of storeAs
                $validatedData['file_path'] = 'uploads/' . $ticket_number . '/' . $file->getClientOriginalName();

                $grievance->update($validatedData);
            }

        } catch (\Throwable $th) {
            DB::rollBack();

            // dd($th)->getMessage();
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }

        DB::commit();

        return redirect()->back()->with('success', 'Grievance created successfully. Your ticket number is: ' . $ticket_number);
    }

    /**
     * Display the specified resource.
     */
    public function show($encryptedId)
    {
        $decryptedId = Crypt::decryptString($encryptedId);
        $grievance = Grievance::findOrFail($decryptedId);
        $grievance->load('transactions');

        $users = User::select('id', 'username')->orderBy('username')->get();

        $isConsumer = auth()->guard('consumer')->check();

        if($isConsumer){
            return view('consumer.grievance.show', compact('grievance', 'users'));
        }
        return view('grievance.show', compact('grievance', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Grievance $grievance)
    {
    
        $validatedData = $request->validate([
            'status' => 'required|in:' . implode(',', Grievance::$statuses),
            'description' => 'required|string',
            'file_upload' => 'nullable|file|max:2048|mimes:jpg,jpeg,png,pdf', // Validate file types
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
                'created_by' => Auth::user()->id,
            ]);

            if ($validatedData['status'] === 'Resolved' || $grievance->status === 'Closed' || $grievance->status === 'Withdrawn') {
                $grievance->update(['priority_score' => 0]);
            }

            // Handle file upload
            if ($request->hasFile('file_upload')) {
                $file = $request->file('file_upload');
                $ticket_number = $grievance->ticket_number; // Assuming ticket_number is already set
                $directoryPath = public_path('uploads/' . $ticket_number);

                // Create a directory for the ticket number if it doesn't exist
                if (!file_exists($directoryPath)) {
                    mkdir($directoryPath, 0755, true);
                }

                // Store the file with the prefix 'resolved_proof_'
                $filePath = $file->move($directoryPath, 'resolved_proof_' . $file->getClientOriginalName());
                $grievance->update(['resolved_file_path' => 'uploads/' . $ticket_number . '/' . 'resolved_proof_' . $file->getClientOriginalName()]);
            }

            DB::commit();

            $encryptedId = Crypt::encryptString($grievance->id);
            
            return redirect()->back()
                ->with('success', 'Grievance updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();

            $encryptedId = Crypt::encryptString($grievance->id);
            return redirect()->route('grievances.show', $encryptedId)
                ->with('error', 'Failed to update grievance: ');
        }
    }

    public function outbox()
    {
        if (request()->ajax()) {
            $grievances = Grievance::whereHas('transactions', function ($query) {
                $query->where('created_by', Auth::user()->id);
            })
            ->select('id', 'consumer_no', 'ca_no', 'ticket_number', 'category', 'name', 'phone', 'priority_score', 'status', 'created_at')
            ->orderByRaw("CASE WHEN status = 'Pending' THEN 0 ELSE 1 END")
            ->orderBy('priority_score', 'desc')
            ->orderBy('created_at', 'desc');

            return datatables()->of($grievances)
                ->addColumn('actions', function ($row) {
                    $encryptedId = Crypt::encryptString($row->id);
                    $btn = '<a href="' . route('grievances.show', $encryptedId) . '" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-1 px-2 rounded-md text-sm">View</a>';
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
        $isAdmin = Auth::user()->hasRole('admin');
        
        // dd($grid_code);
        if (request()->ajax()) {
            $grievances = Grievance::whereHas('transactions', function ($query) use ($isAdmin) {
                    $query->where('assigned_to', Auth::user()->id)
                    ->when($isAdmin, function ($query)  {
                        return $query->orWhere('employee_id' , '!=',  0);
                    }); 
                })
                ->select('id', 'consumer_no', 'ca_no', 'ticket_number', 'category', 'name', 'phone', 'priority_score', 'status', 'created_at')
                ->orderByRaw("CASE WHEN status = 'Pending' THEN 0 ELSE 1 END")
                ->orderBy('priority_score', 'desc')
                ->orderBy('created_at', 'desc');

            return datatables()->of($grievances)
                ->addColumn('actions', function ($row) {
                    $encryptedId = Crypt::encryptString($row->id);
                    $btn = '<a href="' . route('grievances.show', $encryptedId) . '" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-1 px-2 rounded-md text-sm">View</a>';
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

    private function hasDoubleExtension($filename)
    {
        // Check for double extensions
        $parts = pathinfo($filename);
        return isset($parts['extension']) && substr_count($filename, '.') > 1;
    }
}
