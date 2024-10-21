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

