<?php

namespace App\Providers;

use Kreait\Firebase\Factory;
use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Contract\Messaging;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    $this->app->singleton(Messaging::class, function ($app) {
        $serviceAccountPath = storage_path('app/firebase/google-services.json');
        $factory = (new Factory)->withServiceAccount($serviceAccountPath);
        return $factory->createMessaging();
    });
}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
