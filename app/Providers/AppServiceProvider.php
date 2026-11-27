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
        \Illuminate\Support\Facades\Mail::extend('microsoft-graph', fn() => new \App\Mail\Transport\MicrosoftGraphTransport(
            (string) config('services.microsoft_graph.tenant_id'),
            (string) config('services.microsoft_graph.client_id'),
            (string) config('services.microsoft_graph.client_secret'),
            (string) config('services.microsoft_graph.remitente'),
        ));

        // Inicio de sesión con Microsoft (Entra ID) por Socialite
        \Illuminate\Support\Facades\Event::listen(
            \SocialiteProviders\Manager\SocialiteWasCalled::class,
            [\SocialiteProviders\Azure\AzureExtendSocialite::class, 'handle'],
        );

        // Los correos provisionales (@sin-correo.invalid) se quitan de cualquier envío;
        // si no queda nadie a quien mandarlo, el correo no sale
        \Illuminate\Support\Facades\Event::listen(\Illuminate\Mail\Events\MessageSending::class, function ($event) {
            $sufijo = '@' . \App\Models\User::DOMINIO_SIN_CORREO;
            $m = $event->message;
            $quedan = 0;

            foreach (['To' => $m->getTo(), 'Cc' => $m->getCc(), 'Bcc' => $m->getBcc()] as $campo => $direcciones) {
                $reales = array_values(array_filter($direcciones, fn($a) => ! str_ends_with(strtolower($a->getAddress()), $sufijo)));
                $quedan += count($reales);

                if (count($reales) !== count($direcciones)) {
                    $m->getHeaders()->remove($campo);
                    if ($reales) {
                        $m->{strtolower($campo)}(...$reales);
                    }
                }
            }

            if ($quedan === 0) {
                return false; // no se manda
            }
        });
    }
}
