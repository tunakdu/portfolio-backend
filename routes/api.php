<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SkillController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PersonalInfoController;
use App\Http\Controllers\Api\AnalyticsController;

// Herkese Açık Rotalar (Public Routes)
Route::get('/articles', [ArticleController::class, 'index']);
Route::get('/articles/{article:slug}', [ArticleController::class, 'show']);
Route::post('/contact', [MessageController::class, 'store']);
Route::apiResource('/projects', ProjectController::class)->only(['index', 'show']);
Route::apiResource('/skills', SkillController::class)->only(['index']);
Route::apiResource('/categories', CategoryController::class)->only(['index', 'show']);
Route::get('/messages/test-imap', [MessageController::class, 'testImapConnection']);
Route::get('/personal-info/public', [PersonalInfoController::class, 'public']);
Route::get('/personal-info/category/{category}', [PersonalInfoController::class, 'getByCategory']);
Route::get('/personal-info/key/{key}', [PersonalInfoController::class, 'getByKey']);

// CV Public Routes
Route::get('/experiences', [App\Http\Controllers\Api\ExperienceController::class, 'index']);
Route::get('/education', [App\Http\Controllers\Api\EducationController::class, 'index']);

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
    
    // File Upload Routes
    Route::post('/upload', [App\Http\Controllers\Api\UploadController::class, 'upload']);
    Route::delete('/upload', [App\Http\Controllers\Api\UploadController::class, 'delete']);
    
    // Medium Import Routes
    Route::post('/articles/import-medium', [ArticleController::class, 'importMedium']);
    Route::post('/articles/import-file', [ArticleController::class, 'importFile']);

    // Diğer Admin Rotaları
    Route::apiResource('messages', MessageController::class)->only(['index', 'show', 'update', 'destroy']);
    Route::post('/messages/sync-emails', [MessageController::class, 'syncEmails']);
    Route::apiResource('/projects', ProjectController::class)->except(['index', 'show']);
    Route::apiResource('/skills', SkillController::class)->except(['index']);
    
    // Personal Info Admin Routes
    Route::get('/personal-info', [PersonalInfoController::class, 'index']);
    Route::put('/personal-info', [PersonalInfoController::class, 'update']);
    Route::put('/personal-info/key/{key}', [PersonalInfoController::class, 'updateKey']);
    
    // Analytics Admin Routes
    Route::prefix('analytics')->group(function () {
        Route::get('/overview', [AnalyticsController::class, 'overview']);
        Route::get('/visitors-chart', [AnalyticsController::class, 'visitorsChart']);
        Route::get('/top-pages', [AnalyticsController::class, 'topPages']);
        Route::get('/locations', [AnalyticsController::class, 'locations']);
        Route::get('/devices', [AnalyticsController::class, 'devices']);
        Route::get('/referrers', [AnalyticsController::class, 'referrers']);
        Route::get('/recent-activity', [AnalyticsController::class, 'recentActivity']);
    });

    // CV Management Routes
    Route::apiResource('experiences', App\Http\Controllers\Api\ExperienceController::class);
    Route::apiResource('education', App\Http\Controllers\Api\EducationController::class);
});

// Bu rota /auth/me ile aynı işi görüyor, gelecekte birleştirilebilir.
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
