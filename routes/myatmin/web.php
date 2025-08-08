<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthUserController;

// Guest routes: accessible without authentication
Route::get('/login', [AuthUserController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthUserController::class, 'login'])->name('login.post');

// Authenticated routes: require user to be logged in
Route::middleware('auth')->group(function () {

    // User logout
    Route::post('/logout', [AuthUserController::class, 'logout'])->name('logout');

    // Dashboard view
    Route::get('/dashboard', function () {
        return view('pages.dashboard');
    })->name('dashboard');

    // Static pages
    Route::get('/home', function () {
        return view('pages.home');
    })->name('home');

    Route::get('/condo', function () {
        return view('pages.condo');
    })->name('condo');

    // Home data entry form (custom URL: /createhome)
    Route::get('/createhome', function () {
        return view('pages.homecreate');
    })->name('home.create');

    // Temporary POST handler for home.store (no save logic)
    Route::post('/home', function () {
        return redirect()->route('home.create');
    })->name('home.store');
});
