<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect('dashboard');
})->middleware(['auth', 'verified'])->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('endpoints', function () {
    return Inertia::render('Endpoints');
})->middleware(['auth', 'verified'])->name('endpoints');

require __DIR__.'/api.php';

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
