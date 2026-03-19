<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // Importation nécessaire

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Force le HTTPS uniquement en production (sur Railway)
        if (!app()->isLocal()) {
        URL::forceScheme('https');
    }
    }
}
