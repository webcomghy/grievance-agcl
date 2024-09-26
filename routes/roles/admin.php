<?php

use App\Http\Controllers\GrievanceController;
use App\Http\Controllers\MeterUploadController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolePermissionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
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

Route::middleware('auth', 'can:view_meter_uploads')->group(function () {
    Route::get('meter_uploads', [MeterUploadController::class, 'index'])->name('meter_uploads.index');
    Route::get('meter_uploads/set_dates/', [MeterUploadController::class, 'setMonthAndDate'])->name('meter_uploads.set_dates');
    Route::post('meter_uploads/set_dates/', [MeterUploadController::class, 'storeMonthDates'])->name('meter_uploads.set_month_and_date');
});


Route::middleware('auth')->group(function () {
    Route::get('grievances', [GrievanceController::class, 'index'])->name('grievances.index')->middleware('can:view_grivances');
    Route::get('grievances/create', [GrievanceController::class, 'create'])->name('grievances.create');
    Route::get('grievances/{grievance}', [GrievanceController::class, 'show'])->name('grievances.show');
    Route::put('grievances/{grievance}', [GrievanceController::class, 'update'])->name('grievances.update');
    Route::get('inbox', [GrievanceController::class, 'inbox'])->name('grievances.inbox');
    Route::get('outbox', [GrievanceController::class, 'outbox'])->name('grievances.outbox');
});
