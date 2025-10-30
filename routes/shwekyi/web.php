<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\AuthUserController;
use App\Http\Controllers\CondoController;
use App\Http\Controllers\CondoDetailController;

/* simple test */
Route::get('shwekyi-web', fn() => 'Shwe Kyi Web is working');

/* guest routes */
Route::get('/',  [AuthUserController::class, 'showLogin'])->name('login');
Route::post('/', [AuthUserController::class, 'login'])->name('login.post');

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

    // ================== Admin: Condos (header) ==================
    Route::prefix('condos')->group(function () {
        Route::get('/',               [CondoController::class, 'index'])->name('condo');          // list
        Route::get('/create',         [CondoController::class, 'create'])->name('condo.create');  // form
        Route::post('/',              [CondoController::class, 'store'])->name('condo.store');    // save
        Route::get('/{condo}/edit',   [CondoController::class, 'edit'])->name('condo.edit');      // edit form
        Route::match(['put','patch'],'/{condo}', [CondoController::class, 'update'])->name('condo.update'); // 👈
        Route::delete('/{condo}',     [CondoController::class, 'destroy'])->name('condo.destroy');// delete
    });

    // ========== Admin: Condo Details (shallow update/delete) ==========
    Route::post('admin/condo-details/{detail}/update',   [CondoDetailController::class, 'update'])
        ->name('admin.condo-details.update');
    Route::delete('admin/condo-details/{detail}/delete', [CondoDetailController::class, 'destroy'])
        ->name('admin.condo-details.delete');
});
