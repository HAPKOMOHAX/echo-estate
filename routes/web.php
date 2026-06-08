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

