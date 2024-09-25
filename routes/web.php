<?php

use App\Http\Controllers\Auth\ConsumerAuthController;
use App\Http\Controllers\GrievanceController;
use App\Http\Controllers\MeterUploadController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolePermissionController;
use App\Models\Grievance;
use App\Models\GrievanceTransaction;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

require __DIR__ . '/auth.php';

Route::get('/dashboard', function () {

    $userID = Auth::user()->id;
    $user = Auth::user();
    $isAdmin = $user->hasRole('admin'); // Check if the user has the 'admin' role


    $closed = Grievance::select('grid_user', 'status')
        ->when(!$isAdmin, function ($query) use ($userID) {
            return $query->where('grid_user', $userID);
        })->where('status', 'Closed')->count();

    $resolved = Grievance::select('grid_user', 'status')
        ->when(!$isAdmin, function ($query) use ($userID) {
            return $query->where('grid_user', $userID);
        })->where('status', 'Resolved')->count();

    $unread = Grievance::whereDoesntHave('transactions')
        ->where('status', 'Pending')
        ->when(!$isAdmin, function ($query) use ($userID) {
            return $query->where('grid_user', $userID);
        })->count();

    $inprogress = Grievance::select('grid_user', 'status')
        ->when(!$isAdmin, function ($query) use ($userID) {
            return $query->where('grid_user', $userID);
        })->whereIn('status', ['Forwarded', 'Assigned'])->count();

    $total = Grievance::select('id')
        ->when(!$isAdmin, function ($query) use ($userID) {
            return $query->where('grid_user', $userID);
        })->count();

    $recentFive = Grievance::select('ticket_number', 'status', 'category', 'created_at')->orderBy('created_at', 'desc')->limit(5)->get();

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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth', 'can:view_meter_uploads')->group(function () {
    Route::get('meter_uploads', [MeterUploadController::class, 'index'])->name('meter_uploads.index');
    Route::get('meter_uploads/set_dates/', [MeterUploadController::class, 'setMonthAndDate'])->name('meter_uploads.set_dates');
    Route::post('meter_uploads/set_dates/', [MeterUploadController::class, 'storeMonthDates'])->name('meter_uploads.set_month_and_date');
});

Route::get('grievance/form', [GrievanceController::class, 'create'])->name('grievance.form');
Route::post('grievances', [GrievanceController::class, 'store'])->name('grievances.store');
Route::get('grievances/userdata', [GrievanceController::class, 'index'])->name('grievances.indexuser');
Route::get('grievances/userdata/{grievance}', [GrievanceController::class, 'show'])->name('grievances.showuser');
Route::middleware('auth:web,consumer')->group(function () {

    Route::get('/grievances/decode/{ticket_number}', [GrievanceController::class, 'decodeTicket'])->name('grievances.decode')->middleware('can:can_decode_ticket');
});

Route::middleware('auth')->group(function () {
    Route::get('grievances', [GrievanceController::class, 'index'])->name('grievances.index')->middleware('can:view_grivances');
    Route::get('grievances/create', [GrievanceController::class, 'create'])->name('grievances.create');
    Route::get('grievances/{grievance}', [GrievanceController::class, 'show'])->name('grievances.show');
    Route::put('grievances/{grievance}', [GrievanceController::class, 'update'])->name('grievances.update');
    Route::get('inbox', [GrievanceController::class, 'inbox'])->name('grievances.inbox');
    Route::get('outbox', [GrievanceController::class, 'outbox'])->name('grievances.outbox');
});

Route::get('consumer/login', [ConsumerAuthController::class, 'showLoginForm'])->name('consumer.login.form');
Route::post('consumer/login', [ConsumerAuthController::class, 'login'])->name('consumer.login');

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

    Route::post('/consumer/logout', [ConsumerAuthController::class, 'logout'])->name('consumer.logout');
});

Route::middleware(['auth', 'can:manage_roles_and_permissions'])->group(function () {
    Route::get('/roles-permissions', [RolePermissionController::class, 'index'])->name('roles-permissions.index');
    Route::post('/roles', [RolePermissionController::class, 'createRole'])->name('roles.create');
    Route::post('/permissions', [RolePermissionController::class, 'createPermission'])->name('permissions.create');
    Route::post('/roles/assign', [RolePermissionController::class, 'assignRole'])->name('roles.assign');
    Route::post('/permissions/assign', [RolePermissionController::class, 'assignPermission'])->name('permissions.assign');
    Route::get('/users-with-roles', [RolePermissionController::class, 'getUsersWithRoles'])->name('users.with.roles');
    Route::post('/roles/remove', [RolePermissionController::class, 'removeRole'])->name('roles.remove');
    Route::get('/roles/permissions', [RolePermissionController::class, 'getRolePermissions'])->name('roles.permissions');
});
