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
        // This registers the broadcasting routes
        Broadcast::routes();

        // Load the channels.php file where you define your channels
        require base_path('routes/channels.php');
    }
}
