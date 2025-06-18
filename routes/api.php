<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\SiteSettingController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SkillController;

Route::post('/contact', [MessageController::class, 'store']);
Route::get('/site-settings', [SiteSettingController::class, 'index']);
Route::apiResource('/projects', ProjectController::class)->only(['index', 'show']);
Route::apiResource('/skills', SkillController::class)->only(['index', 'show']);

// Auth routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    // Public routes
    Route::get('/home', function () {
        return response()->json(['message' => 'Portfolio API v1']);
    });

    // Authentication routes
    Route::post('/auth/login', function () {
        return response()->json(['message' => 'Login endpoint']);
    });

    // Portfolio content routes
    Route::get('/about', function () {
        return response()->json(['message' => 'About page content']);
    });

    Route::get('/projects', function () {
        return response()->json(['message' => 'Projects list']);
    });

    // Protected admin routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('admin')->group(function () {
            Route::get('/dashboard', function () {
                return response()->json(['message' => 'Admin dashboard']);
            });

            Route::apiResource('projects', \App\Http\Controllers\Api\ProjectController::class);
            Route::apiResource('messages', \App\Http\Controllers\Api\MessageController::class);
            Route::apiResource('skills', \App\Http\Controllers\Api\SkillController::class);
        });
    });
});
