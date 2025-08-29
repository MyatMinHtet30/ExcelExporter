<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\AuthUserController;

Route::middleware('web')->group(function () {
    Route::get('lang/{locale}', function (string $locale) {
        if (! in_array($locale, ['en', 'th'], true)) {
            $locale = 'en';
        }
        Session::put('locale', $locale);
        return redirect()->back();
    })->name('lang.switch');
});

Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/logout', [AuthUserController::class, 'logout'])->name('logout');

    Route::get('/home', function () {
        App::setLocale(Session::get('locale', config('app.locale')));
        return view('pages.home');
    })->name('home');

    Route::get('/createhome', function () {
        App::setLocale(Session::get('locale', config('app.locale')));
        return view('pages.homecreate');
    })->name('home.create');

    Route::post('/home', function () {
        return redirect()->route('home.create');
    })->name('home.store');
});
