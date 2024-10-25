<?php

use App\Http\Controllers\GrievanceController;
use App\Http\Controllers\MeterUploadController;
use App\Models\Grievance;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {

    $userID = Auth::user()->id;
    $gridCode = Auth::user()->grid_code;
    $user = Auth::user();
    $isAdmin = $user->hasRole('admin');
    $isNodalOfficer = $user->hasRole('nodal_officer');
    $isSupport = $user->hasRole('support');


    $closed = Grievance::select('grid_user', 'grid_code', 'status')
        ->when(!$isAdmin && !$isNodalOfficer && !$isSupport, function ($query) use ($userID, $gridCode) {
            return $query->where('grid_user', $userID)
                ->orWhere('grid_code', $gridCode);
        })->where('status', 'Closed')->count();

    $resolved = Grievance::select('grid_user', 'grid_code', 'status')
        ->when(!$isAdmin && !$isNodalOfficer && !$isSupport, function ($query) use ($userID, $gridCode) {
            return $query->where('grid_user', $userID)
                ->orWhere('grid_code', $gridCode);
        })->where('status', 'Resolved')->count();

    $unread = Grievance::whereDoesntHave('transactions')
        ->where('status', 'Pending')
        ->when(!$isAdmin && !$isNodalOfficer && !$isSupport, function ($query) use ($userID, $gridCode) {
            return $query->where('grid_user', $userID)
                ->orWhere('grid_code', $gridCode);
        })->count();

    $inprogress = Grievance::select('grid_user', 'grid_code', 'status')
        ->when(!$isAdmin && !$isNodalOfficer && !$isSupport, function ($query) use ($userID, $gridCode) {
            return $query->where('grid_user', $userID)
                ->orWhere('grid_code', $gridCode);
        })->whereIn('status', ['Forwarded', 'Assigned'])->count();

    $total = Grievance::select('id')
        ->when(!$isAdmin && !$isNodalOfficer && !$isSupport, function ($query) use ($userID, $gridCode) {
            return $query->where('grid_user', $userID)
                ->orWhere('grid_code', $gridCode);
        })->count();

    $recentFive = Grievance::select('ticket_number', 'status', 'category', 'created_at')
        ->when(!$isAdmin && !$isNodalOfficer && !$isSupport, function ($query) {
            return $query->where('grid_code', Auth::user()->grid_code)
                ->orWhere('grid_user', Auth::user()->id);
        })
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

    $resolvedGrievances = Grievance::select('created_at', 'updated_at')
        ->whereIn('status', ['Resolved', 'Closed'])
        ->get();

    $totalTimeDifferenceInSeconds = 0;
    $totalGrievances = $resolvedGrievances->count();

    foreach ($resolvedGrievances as $grievance) {
        // Calculate the difference in seconds between created_at and updated_at
        $totalTimeDifferenceInSeconds += $grievance->updated_at->diffInSeconds($grievance->created_at);
    }

    if ($totalGrievances > 0) {
        // Calculate the average time difference in seconds
        $averageTimeDifferenceInSeconds = $totalTimeDifferenceInSeconds / $totalGrievances;

        // Convert to a CarbonInterval for human-readable format
        $averageTimeInterval = CarbonInterval::seconds($averageTimeDifferenceInSeconds)->cascade();

        // Output in a human-readable format
        $averageTimeFormatted = $averageTimeInterval->forHumans();
    } else {
        $averageTimeFormatted = 0;
    }

    return view('dashboard', compact(
        'closed',
        'resolved',
        'unread',
        'inprogress',
        'recentFive',
        'total',
        'averageTimeFormatted'
    ));
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth:consumer')->group(function () {
    Route::get('/consumer/dashboard', function () {
        $userID = auth()->guard('consumer')->user()->id;

        $unread = Grievance::where('consumer_id', $userID)->whereDoesntHave('transactions')->count();
        $closed = Grievance::where('consumer_id', $userID)->where('status', 'Closed')->count();
        $resolved = Grievance::where('consumer_id', $userID)->where('status', 'Resolved')->count();
        $inprogress = Grievance::where('consumer_id', $userID)->whereIn('status', ['Forwarded', 'Assigned'])->count();
        $total = Grievance::where('consumer_id', $userID)->count();

        $recentFive = Grievance::select('consumer_id','ticket_number', 'status', 'category', 'created_at')->orderBy('created_at', 'desc')
            ->where('consumer_id', $userID)
            ->limit(5)
            ->get();

        return view('consumer.dashboard', 
            compact(
                'unread', 
                'closed', 
                'resolved', 
                'inprogress', 
                'total',
                'recentFive'
        ));
    })->name('consumer.dashboard');
});
