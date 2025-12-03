<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\ProfileController;

// Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', function (Request $request) {
        return $request->user();
    });
    Route::post('/up-profile', [ProfileController::class, 'update']);

    //CRUD Karyawan
    Route::apiResource('karyawans', KaryawanController::class);

    //Absen
    Route::prefix('attendance')->group(function () {
        Route::post('/clock-in', [AttendanceController::class, 'clockIn']);
        Route::post('/clock-out', [AttendanceController::class, 'clockOut']);
        Route::get('/status', [AttendanceController::class, 'status']);
        Route::get('/today', [AttendanceController::class, 'todayRecap']);
        Route::get('/history', [AttendanceController::class, 'index']);
    });

    //Leave
    Route::prefix('leaves')->group(function () {
        Route::post('/', [LeaveController::class, 'store']);  
        Route::get('/my', [LeaveController::class, 'myLeaves']);      
        Route::get('/all', [LeaveController::class, 'index']);          
        Route::put('/{id}/status', [LeaveController::class, 'updateStatus']); 
    });
});