<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\AuthUserController;
use Illuminate\Support\Facades\Session;

/* simple test */
Route::get('shwekyi-web', fn() => 'Shwe Kyi Web is working');

/* guest routes */
Route::get('/',  [AuthUserController::class, 'showLogin'])->name('login');
Route::post('/',  [AuthUserController::class, 'login'])->name('login.post');


Route::get('lang/{locale}', function (string $locale) {
    if (! in_array($locale, ['en', 'th'], true)) {
        $locale = 'en';
    }
    Session::put('locale', $locale);
    return redirect()->back();
})->name('lang.switch');

/* protected routes */
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthUserController::class, 'logout'])->name('logout');

    Route::get('/dashboard', function () {
        App::setLocale(Session::get('locale', config('app.locale')));
        return view('pages.dashboard');
    })->name('dashboard');

    Route::get('/codo', function(){
        App::setLocale(Session::get('locale', config('app.locale')));
        return view('pages.condo');
    })->name('condo');

    Route::get('/createcondo', function(){
        App::setLocale(Session::get('locale', config('app.locale')));
        return view('pages.createcondo');
    })->name('condo-form');
});
