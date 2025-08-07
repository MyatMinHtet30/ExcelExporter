<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthUserController;

/* simple test */
Route::get('shwekyi-web', fn() => 'Shwe Kyi Web is working');


/* guest routes */
Route::get('/login',  [AuthUserController::class, 'showLogin'])->name('login');
Route::post('/login',  [AuthUserController::class, 'login'])->name('login.post');

/* protected routes */
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthUserController::class, 'logout'])->name('logout');

    Route::get('/dashboard', function () {
        return view('pages.dashboard');
    })->name('dashboard');

    Route::get('/home', function(){
        return view('pages.home');
    })->name('home');

    Route::get('/codo', function(){
        return view('pages.condo');
    })->name('condo');

    Route::get('/createcondo', function(){
        return view('pages.createcondo');
    })->name('condo-form');
});
