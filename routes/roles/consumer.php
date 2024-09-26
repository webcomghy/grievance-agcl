<?php

use App\Http\Controllers\GrievanceController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:web,consumer')->group(function () {
    Route::get('/grievances/decode/{ticket_number}', [GrievanceController::class, 'decodeTicket'])->name('grievances.decode')->middleware('can:can_decode_ticket');
    Route::get('grievances/userdata', [GrievanceController::class, 'index'])->name('grievances.indexuser');
    Route::get('grievances/userdata/{grievance}', [GrievanceController::class, 'show'])->name('grievances.showuser');
});