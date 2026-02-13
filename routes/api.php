<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TodoController;
use App\Http\Controllers\Api\SecureNoteController;
use App\Http\Controllers\Api\SecurePinController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Health check
Route::get('health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is running',
        'timestamp' => now()->toIso8601String(),
    ]);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);

    Route::get('todos', [TodoController::class, 'index']);
    Route::post('todos', [TodoController::class, 'store']);
    Route::get('todos/statistics', [TodoController::class, 'statistics']);
    Route::get('todos/{id}', [TodoController::class, 'show']);
    Route::put('todos/{id}', [TodoController::class, 'update']);
    Route::patch('todos/{id}/toggle', [TodoController::class, 'toggle']);
    Route::delete('todos/{id}', [TodoController::class, 'destroy']);
    Route::get('secure-notes', [SecureNoteController::class, 'index']);
    Route::post('secure-notes', [SecureNoteController::class, 'store']);
    Route::get('secure-notes/{id}', [SecureNoteController::class, 'show']);
    Route::put('secure-notes/{id}', [SecureNoteController::class, 'update']);
    Route::delete('secure-notes/{id}', [SecureNoteController::class, 'destroy']);
    Route::patch('secure-notes/{id}/favorite', [SecureNoteController::class, 'toggleFavorite']);
    Route::get('secure-pin/check', [SecurePinController::class, 'checkPin']);
    Route::post('secure-pin/set', [SecurePinController::class, 'setPin']);
    Route::post('secure-pin/verify', [SecurePinController::class, 'verifyPin']);
    Route::post('secure-pin/reset', [SecurePinController::class, 'resetPin']);
    Route::post('secure-pin/change', [SecurePinController::class, 'changePin']);
});
