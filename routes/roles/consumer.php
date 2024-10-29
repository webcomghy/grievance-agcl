<?php

use App\Http\Controllers\GrievanceController;
use App\Http\Controllers\MeterUploadController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:consumer')->group(function () {
    Route::get('grievancesuser', [GrievanceController::class, 'index'])->name('grievances.indexuser');
    Route::get('grievancesuser/data/{grievance}', [GrievanceController::class, 'show'])->name('grievances.showuser');
    Route::get('meter_uploads_user', [MeterUploadController::class, 'index'])->name('meter_uploads.indexuser');
    Route::put('withdraw/{grievance}', [GrievanceController::class, 'update'])->name('grievances.withdraw');
});

Route::middleware(['web'])->group(function () {
    Route::get('grievanceotp/form', [GrievanceController::class, 'create'])->name('grievance.otp.form');
    Route::post('grievancesotp', [GrievanceController::class, 'store'])->name('grievances.otp.store');
    Route::get('grievancesotp', [GrievanceController::class, 'index'])->name('grievances.indexotp');
    Route::get('grievancesotp/data/{grievance}', [GrievanceController::class, 'show'])->name('grievances.showotp');
    Route::put('withdrawotp/{grievance}', [GrievanceController::class, 'update'])->name('grievances.withdrawotp');
    Route::get('meter_uploads_otp', [MeterUploadController::class, 'index'])->name('meter_uploads.indexotp');
});
