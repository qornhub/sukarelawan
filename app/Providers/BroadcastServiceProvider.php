<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register the broadcasting routes (/broadcasting/auth etc.)
        Broadcast::routes();

        // Load your channel definitions from routes/channels.php
        require base_path('routes/channels.php');
    }
}
