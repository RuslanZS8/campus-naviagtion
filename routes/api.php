<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\LocationController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\MediaController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\CampusController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ─── Existing Frontend Routes ───
Route::get('/nodes', [CampusController::class, 'nodes']);
Route::get('/route', [CampusController::class, 'route']);

// ─── V1 Public Routes ───
Route::prefix('v1')->group(function () {
    Route::get('/locations', [LocationController::class, 'index']);
    Route::get('/locations/{id}', [LocationController::class, 'show']);
    Route::get('/locations/{id}/media', [LocationController::class, 'media']);
    Route::get('/locations/{id}/matterport', [LocationController::class, 'matterport']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{slug}', [CategoryController::class, 'show']);
});

// ─── Admin Login (public) ───
Route::post('/v1/admin/login', [AuthController::class, 'login']);

// ─── V1 Admin Routes ───
Route::prefix('v1/admin')
    ->middleware(['auth:sanctum', 'admin'])
    ->group(function () {
        // Auth
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        // Locations
        Route::get('/locations', [LocationController::class, 'adminIndex']);
        Route::post('/locations', [LocationController::class, 'store']);
        Route::put('/locations/{id}', [LocationController::class, 'update']);
        Route::delete('/locations/{id}', [LocationController::class, 'destroy']);
        Route::patch('/locations/{id}/toggle-visibility', [LocationController::class, 'toggleVisibility']);
        Route::patch('/locations/reorder', [LocationController::class, 'reorder']);

        // Categories
        Route::get('/categories', [CategoryController::class, 'adminIndex']);
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{id}', [CategoryController::class, 'update']);
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

        // Media
        Route::post('/media', [MediaController::class, 'store']);
        Route::delete('/media/{id}', [MediaController::class, 'destroy']);
        Route::patch('/media/reorder', [MediaController::class, 'reorder']);
    });
