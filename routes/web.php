<?php

use App\Http\Controllers\GrievanceController;
use App\Http\Controllers\MeterUploadController;
use App\Http\Controllers\ProfileController;
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

Route::middleware('auth')->group(function () {
    Route::get('meter_uploads', [MeterUploadController::class, 'index'])->name('meter_uploads.index');
    Route::get('meter_uploads/create', [MeterUploadController::class, 'create'])->name('meter_uploads.create');
    Route::post('meter_uploads', [MeterUploadController::class, 'store'])->name('meter_uploads.store');
    Route::get('meter_uploads/{meterUpload}', [MeterUploadController::class, 'show'])->name('meter_uploads.show');
    Route::get('meter_uploads/{meterUpload}/edit', [MeterUploadController::class, 'edit'])->name('meter_uploads.edit');
    Route::put('meter_uploads/{meterUpload}', [MeterUploadController::class, 'update'])->name('meter_uploads.update');
    Route::delete('meter_uploads/{meterUpload}', [MeterUploadController::class, 'destroy'])->name('meter_uploads.destroy');
});


Route::get('grievance/form', [GrievanceController::class, 'create'])->name('grievance.form');
Route::post('send-otp', [GrievanceController::class, 'sendOtp'])->name('send.otp');
Route::post('verify-otp', [GrievanceController::class, 'verifyOtp'])->name('verify.otp');
Route::post('grievances', [GrievanceController::class, 'store'])->name('grievances.store');

Route::middleware('auth')->group(function () {
    Route::get('grievances', [GrievanceController::class, 'index'])->name('grievances.index');
});

require __DIR__.'/auth.php';
