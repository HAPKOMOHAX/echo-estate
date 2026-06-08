<?php

use App\Http\Controllers\PropertySearchController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Properties/Index');
})->name('properties.index');

Route::get('/properties', function () {
    return redirect()->route('properties.index');
});

Route::get('/properties/search', [PropertySearchController::class, 'index'])
    ->name('properties.search');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('properties.index');
    })->name('dashboard');
});
