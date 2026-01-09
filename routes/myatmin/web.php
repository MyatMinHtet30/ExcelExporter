<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\AuthUserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HomeDetailController;

Route::middleware('web')->group(function () {
    Route::get('lang/{locale}', function (string $locale) {
        if (! in_array($locale, ['en', 'th'], true)) {
            $locale = 'en';
        }
        Session::put('locale', $locale);
        return redirect()->back();
    })->name('lang.switch');
});

Route::middleware(['web','auth'])->group(function () {
    Route::get('/dashboard', function () {
        App::setLocale(Session::get('locale', config('app.locale')));

        return view('pages.dashboard');
    })->name('dashboard');
    Route::post('/logout', [AuthUserController::class, 'logout'])->name('logout');

    Route::post('homes/preview', [HomeController::class, 'preview'])
        ->name('home.preview');

    Route::delete('homes/photos/{image}', [HomeController::class, 'deletePhoto'])
        ->name('home.photo.delete');

    Route::get('homes/{home}/export/pdf', [HomeController::class, 'exportPdf'])
        ->name('home.export.pdf');

    Route::get('homes/{home}/export/excel', [HomeController::class, 'exportExcel'])
        ->name('home.export.excel');


    Route::resource('homes', HomeController::class)
    ->only(['index','create','store','edit','update','destroy'])
    ->names([
        'index'   => 'home',
        'create'  => 'home.create',
        'store'   => 'home.store',
        'edit'    => 'home.edit',
        'update'  => 'home.update',
        'destroy' => 'home.destroy',
    ]);

    Route::resource('homes.details', HomeDetailController::class)
        ->parameters(['details' => 'detail'])
        ->only(['index','store','edit','update','destroy'])
        ->names([
            'index'   => 'home.details.index',
            'store'   => 'home.details.store',
            'edit'    => 'home.details.edit',
            'update'  => 'home.details.update',
            'destroy' => 'home.details.destroy',
        ]);

    // Optional alias to keep /createhome working
    Route::get('/createhome', fn () => redirect()->route('home.create'));
});
