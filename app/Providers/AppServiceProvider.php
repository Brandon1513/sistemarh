<?php

namespace App\Providers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Factory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Messaging::class, function ($app) {
            $serviceAccountPath = storage_path(env('FIREBASE_CREDENTIALS', 'app/firebase/firebase-credentials.json'));
            $factory = (new Factory)->withServiceAccount($serviceAccountPath);
            return $factory->createMessaging();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Los Resources de la API se devuelven sin la envoltura {"data": ...}
        JsonResource::withoutWrapping();
    }
}