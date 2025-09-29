<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\HomeController;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\Attendances\AttendanceController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Login route
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->prefix('ngo')->name('ngo.')->group(function () {
    Route::post('events/{event}/scan', [AttendanceController::class, 'scan'])->name('attendance.scan'); 
    

    
});

// Volunteer routes
Route::middleware('auth:sanctum')->prefix('volunteer')->group(function () {
     Route::get('profile', [HomeController::class, 'profile']);
    Route::get('events/upcoming', [HomeController::class, 'upcoming']);
    Route::get('events/attended', [HomeController::class, 'attended']);
});