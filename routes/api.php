<?php

use App\Http\Controllers\Api\AuthenticationTypesController;
use App\Http\Controllers\Api\EndpointsController;
use App\Http\Controllers\Api\LogsController;
use App\Http\Controllers\Api\ReportRangesController;
use App\Http\Controllers\Api\RequestTypesController;
use App\Http\Controllers\Api\SplitTypesController;
use App\Http\Controllers\Api\StatisticsController;
use App\Http\Controllers\Api\StatusCodesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->middleware(['auth:sanctum', 'verified'])->group(function () {
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

    Route::patch('/endpoints/{endpoint}/toggle-visibility', [EndpointsController::class, 'toggleVisibility'])
        ->name('api.endpoints.toggleVisibility');

    /**
     * Helper routes
     */
    Route::get('/status-codes', StatusCodesController::class)
        ->name('api.statusCodes.index');

    Route::get('/report-ranges', ReportRangesController::class)
        ->name('api.reportRanges.index');

    Route::get('/split-types', SplitTypesController::class)
        ->name('api.splitTypes.index');

    Route::get('/request-types', RequestTypesController::class)
        ->name('api.requestTypes.index');

    Route::get('/auth-types', AuthenticationTypesController::class)
        ->name('api.authTypes.index');

    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    })->name('api.user');

    /** End helper routes */
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
});
