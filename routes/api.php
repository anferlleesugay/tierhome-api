<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RentalPropertyController;

// Public routes (no token needed)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Public property browsing (tenants can search without logging in)
Route::get('/properties',      [RentalPropertyController::class, 'index']);
Route::get('/properties/{id}', [RentalPropertyController::class, 'show']);

// Protected routes (token required)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // Landlord property management
    Route::post('/properties',           [RentalPropertyController::class, 'store']);
    Route::put('/properties/{id}',       [RentalPropertyController::class, 'update']);
    Route::delete('/properties/{id}',    [RentalPropertyController::class, 'destroy']);
});