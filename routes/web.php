<?php

use App\Http\Controllers\Auth\ConsumerAuthController;
use App\Http\Controllers\GrievanceController;
use App\Http\Controllers\MeterUploadController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolePermissionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth', 'can:view_meter_uploads')->group(function () {
    Route::get('meter_uploads', [MeterUploadController::class, 'index'])->name('meter_uploads.index');
    Route::get('meter_uploads/create', [MeterUploadController::class, 'create'])->name('meter_uploads.create');
    Route::post('meter_uploads', [MeterUploadController::class, 'store'])->name('meter_uploads.store');
    Route::get('meter_uploads/{meterUpload}', [MeterUploadController::class, 'show'])->name('meter_uploads.show');
    Route::get('meter_uploads/{meterUpload}/edit', [MeterUploadController::class, 'edit'])->name('meter_uploads.edit');
    Route::put('meter_uploads/{meterUpload}', [MeterUploadController::class, 'update'])->name('meter_uploads.update');
    Route::delete('meter_uploads/{meterUpload}', [MeterUploadController::class, 'destroy'])->name('meter_uploads.destroy');
});

Route::middleware('auth:consumer')->group(function () {
    Route::get('grievance/form', [GrievanceController::class, 'create'])->name('grievance.form');
    Route::post('grievances', [GrievanceController::class, 'store'])->name('grievances.store');
});

Route::middleware('auth','can:view_grivances')->group(function () {
    Route::get('grievances', [GrievanceController::class, 'index'])->name('grievances.index');
    Route::get('grievances/create', [GrievanceController::class, 'create'])->name('grievances.create');
    Route::get('grievances/{grievance}', [GrievanceController::class, 'show'])->name('grievances.show');
    Route::put('grievances/{grievance}', [GrievanceController::class, 'update'])->name('grievances.update');
});

require __DIR__.'/auth.php';

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

