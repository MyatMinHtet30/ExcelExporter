<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthUserController;

/* guest routes */
Route::get('/login',  [AuthUserController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthUserController::class, 'login'])->name('login.post');

/* protected routes */
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthUserController::class, 'logout'])->name('logout');

    Route::get('/dashboard', function () {
        return view('pages.dashboard');
    })->name('dashboard');

    Route::get('/home', function () {
        return view('pages.home');
    })->name('home');

    Route::get('/condo', function () {
        return view('pages.condo');
    })->name('condo');

    // Create new home data form
    Route::get('/home/create', function () {
        return view('pages.homecreate');
    })->name('home.create');

    // Dummy POST route to prevent 'home.store' route error
    Route::post('/home', function () {
        // Just redirect back to the create form without validation or saving
        return redirect()->route('home.homecreate');
    })->name('home.store');
});


