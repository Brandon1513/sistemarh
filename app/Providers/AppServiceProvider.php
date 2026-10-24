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
        // Correo con Microsoft Graph, desde el buzón de la intranet (MAIL_MAILER=microsoft-graph)
        \Illuminate\Support\Facades\Mail::extend('microsoft-graph', fn () => new \App\Mail\Transport\MicrosoftGraphTransport(
            (string) config('services.microsoft_graph.tenant_id'),
            (string) config('services.microsoft_graph.client_id'),
            (string) config('services.microsoft_graph.client_secret'),
            (string) config('services.microsoft_graph.remitente'),
        ));
    }
}