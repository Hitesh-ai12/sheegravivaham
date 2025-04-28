<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\CustomAuthController;

// Get Authenticated User
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json($request->user());
});

Route::post('/auth/select-language', [CustomAuthController::class, 'selectLanguage']);

Route::post('/auth/account-selection', [CustomAuthController::class, 'accountSelection']);
Route::post('/auth/signup', [CustomAuthController::class, 'signup']);
Route::post('/auth/verify-otp', [CustomAuthController::class, 'verifyOtp']);
Route::post('/auth/login', [CustomAuthController::class, 'login']);
Route::post('/auth/forgot-password', [CustomAuthController::class, 'forgotPassword']);
Route::post('/auth/reset-password', [CustomAuthController::class, 'resetPassword']);
Route::post('/auth/change-password', [CustomAuthController::class, 'changePassword'])->middleware('auth:sanctum');
