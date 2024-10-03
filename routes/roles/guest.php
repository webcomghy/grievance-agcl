<?php

use App\Http\Controllers\GrievanceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('grievance/form', [GrievanceController::class, 'create'])->name('grievance.form');
Route::post('grievances', [GrievanceController::class, 'store'])->name('grievances.store');