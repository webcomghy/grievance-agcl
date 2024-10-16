<?php

namespace App\Http\Controllers;

use App\Models\GridMaster;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::select('username', 'created_at'); // Adjust fields as necessary
            return datatables()->of($data)
                ->addColumn('actions', function ($row) {
                    return '<button class="bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-2 rounded" onclick="deleteUser(\'' . $row->username . '\')"><i class="fa fa-trash"></i></button>'; // Changed button color to red
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

    }
    public function create()
    {
        $gridMaster = GridMaster::select('GRID_ID')->get();

        // dd($gridMaster->take(10));
        return view('admin.create-user', compact('gridMaster')); // Adjust the path as necessary
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:grid_admins', // Change to grid_admins
            'password' => 'required|string|min:8|confirmed',
            'grid_id' => 'required',
        ]);

        DB::beginTransaction();
        try {
            DB::table('grid_admins')->insert([
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'grid_id' => $request->grid_id,
                'created_at' => now(), // Optional: Add timestamps if needed
                'updated_at' => now(), // Optional: Add timestamps if needed
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Failed to create user.');
        }
        

        return redirect()->route('users.create')->with('success', 'User created successfully.');
    }

    public function destroy($username)
    {
        try {
            // Attempt to delete the user from the grid_admins table using username
            DB::table('grid_admins')->where('username', $username)->delete();

            return response()->json(['message' => 'User deleted successfully.']);
        } catch (\Throwable $th) {
            // Handle any errors that occur during the deletion
            return response()->json(['message' => 'Failed to delete user.'], 500);
        }
    }

   
}
