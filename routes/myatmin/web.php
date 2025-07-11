<?php

use Illuminate\Support\Facades\Route;


Route::get('myatmin-web', function () {
    return "Web is working";
})->name('myatmin.api');
