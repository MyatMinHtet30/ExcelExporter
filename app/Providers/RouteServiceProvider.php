<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // ✅ Load custom route file
        $this->loadRoutesFrom(base_path('routes/myatmin/web.php'));
    }
}
