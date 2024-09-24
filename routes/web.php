<?php

use App\Http\Controllers\Auth\ConsumerAuthController;
use App\Http\Controllers\GrievanceController;
use App\Http\Controllers\MeterUploadController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolePermissionController;
use App\Models\Grievance;
use App\Models\GrievanceTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

require __DIR__.'/auth.php';

Route::get('/dashboard', function () {
    
    $userID = Auth::user()->id;

    $closed = GrievanceTransaction::where('created_by', $userID)->where('status', 'Closed')->count();
    $resolved = GrievanceTransaction::where('created_by', $userID)->where('status', 'Resolved')->count();
    $unread = Grievance::whereDoesntHave('transactions')->where('status', 'Pending')->count();
    $inprogress = GrievanceTransaction::where('created_by', $userID)->whereIn('status', ['Forwarded', 'Assigned'])->groupBy('grievance_id')->count();
    $total = Grievance::all()->count();

    $recentFive = Grievance::orderBy('created_at', 'desc')->limit(5)->get();

     // Fetch resolved grievances
    $resolvedGrievances = Grievance::whereIn('status', ['Resolved', 'Closed'])->get();

     // Calculate total resolution time
     $totalResolutionTime = $resolvedGrievances->sum(function ($grievance) {
         return $grievance->updated_at->diffInSeconds($grievance->created_at);
     });
 
     // Calculate average resolution time
     $averageResolutionTime = $resolvedGrievances->count() > 0 ? $totalResolutionTime / $resolvedGrievances->count() : 0;
 
     // Convert average time to a more readable format (e.g., days, hours, minutes)
     $averageTimeFormatted = gmdate("H:i:s", $averageResolutionTime);
    
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
        return view('consumer.dashboard');
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

