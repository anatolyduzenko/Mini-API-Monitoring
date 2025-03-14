<?php

use App\Http\Controllers\Api\EndpointsController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group( function() {

    Route::get('/endpoints', [EndpointsController::class, 'index'])
        ->middleware(['auth', 'verified'])
        ->name('api.endpoints.index');

    Route::post('/endpoints', [EndpointsController::class, 'store'])
        ->middleware(['auth', 'verified'])
        ->name('api.endpoints.store');

    Route::delete('/endpoints/{apiEndpoint}', [EndpointsController::class, 'destroy'])
        ->middleware(['auth', 'verified'])
        ->name('api.endpoints.destroy');

    Route::get('/endpoints/{apiEndpoint}', [EndpointsController::class, 'show'])
        ->middleware(['auth', 'verified'])
        ->name('api.endpoints.show');

    Route::put('/endpoints/{apiEndpoint}', [EndpointsController::class, 'update'])
        ->middleware(['auth', 'verified'])
        ->name('api.endpoints.update');
        
});
