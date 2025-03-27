<?php

use App\Http\Controllers\Api\EndpointsController;
use App\Http\Controllers\Api\LogsController;
use App\Http\Controllers\Api\StatisticsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {

    Route::get('/endpoints', [EndpointsController::class, 'index'])
        ->name('api.endpoints.index');

    Route::post('/endpoints', [EndpointsController::class, 'store'])
        ->name('api.endpoints.store');

    Route::delete('/endpoints/{endpoint}', [EndpointsController::class, 'destroy'])
        ->name('api.endpoints.destroy');

    Route::get('/endpoints/{endpoint}', [EndpointsController::class, 'show'])
        ->name('api.endpoints.show');

    Route::put('/endpoints/{endpoint}', [EndpointsController::class, 'update'])
        ->name('api.endpoints.update');

    Route::get('/logs', LogsController::class)
        ->name('api.logs.index');

    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    })->name('api.user');

    Route::prefix('statistics')->group(function () {

        Route::get('/uptime', [StatisticsController::class, 'getUptime'])
            ->name('api.statistics.uptime');

        Route::get('/uptime-graph', [StatisticsController::class, 'getUptimeGraph'])
            ->name('api.statistics.uptimeGraph');

        Route::get('/response-time', [StatisticsController::class, 'getResponseTime'])
            ->name('api.statistics.responseTime');

        Route::get('/recent-logs', [StatisticsController::class, 'getRecentLogs'])
            ->name('api.statistics.recent');

    });

})->middleware(['auth', 'verified']);
