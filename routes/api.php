<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Login route
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->prefix('ngo')->name('ngo.')->group(function () {
    Route::post('events/{event}/scan', [\App\Http\Controllers\Attendances\AttendanceController::class, 'scan'])
        ->name('attendance.scan');
});
