<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthUserController;

// Authenticated routes: require user to be logged in
Route::middleware('auth')->group(function () {

    // User logout
    Route::post('/logout', [AuthUserController::class, 'logout'])->name('logout');

    // Static pages
    Route::get('/home', function () {
        return view('pages.home');
    })->name('home');

    // Home data entry form (custom URL: /createhome)
    Route::get('/createhome', function () {
        return view('pages.homecreate');
    })->name('home.create');

    // Temporary POST handler for home.store (no save logic)
    Route::post('/home', function () {
        return redirect()->route('home.create');
    })->name('home.store');
});
