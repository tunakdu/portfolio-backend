<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\SiteSettingController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SkillController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\CategoryController;

// Herkese Açık Rotalar (Public Routes)
Route::get('/articles', [ArticleController::class, 'index']);
Route::get('/articles/{article:slug}', [ArticleController::class, 'show']);
Route::post('/contact', [MessageController::class, 'store']);
Route::get('/site-settings', [SiteSettingController::class, 'index']);
Route::apiResource('/projects', ProjectController::class)->only(['index', 'show']);
Route::apiResource('/skills', SkillController::class)->only(['index']);
Route::apiResource('/categories', CategoryController::class)->only(['index', 'show']);

// Kimlik Doğrulama Rotaları (Authentication Routes)
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

// Kimlik Doğrulaması Gerektiren Admin Rotaları (Authenticated Admin Routes)
Route::middleware('auth:sanctum')->group(function () {
    // Makale Yönetimi
    Route::post('/articles', [ArticleController::class, 'store']);
    Route::put('/articles/{article:slug}', [ArticleController::class, 'update']);
    Route::delete('/articles/{article:slug}', [ArticleController::class, 'destroy']);
    Route::post('/articles/upload-cover', [ArticleController::class, 'uploadCoverImage']);

    // Diğer Admin Rotaları
    Route::apiResource('messages', MessageController::class)->only(['index', 'show', 'update', 'destroy']);
    Route::post('/site-settings', [SiteSettingController::class, 'store']);
    Route::apiResource('/projects', ProjectController::class)->except(['index', 'show']);
    Route::apiResource('/skills', SkillController::class)->except(['index']);
});

// Bu rota /auth/me ile aynı işi görüyor, gelecekte birleştirilebilir.
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
