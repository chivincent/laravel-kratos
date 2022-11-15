<?php

declare(strict_types = 1);

namespace Chivincent\LaravelKratos;

use Chivincent\LaravelKratos\UserProvider\KratosDatabaseUserProvider;
use Chivincent\LaravelKratos\UserProvider\KratosUserProvider;
use GuzzleHttp\Client;
use Illuminate\Auth\AuthManager;
use Illuminate\Auth\RequestGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Ory\Kratos\Client\Api\V0alpha2Api as KratosApi;
use Ory\Kratos\Client\Configuration;

class KratosServiceProvider extends ServiceProvider
{
    public function register()
    {
        if (! $this->app->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__ . '/../config/kratos.php', 'kratos');
        }

        config([
            'auth.providers' => [
                ...config('kratos.user_providers'),
                ...config('auth.providers'),
            ],
        ]);

        $this->app->singleton(
            KratosApi::class,
            fn () => new KratosApi(
                new Client(config('kratos.client_options')),
                Configuration::getDefaultConfiguration()->setHost(config('kratos.endpoints.public'))
            )
        );
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/kratos.php' => config_path('kratos.php'),
            ], 'kratos-config');
        }

        $this->configureAuthManager();
    }

    protected function configureAuthManager()
    {
        Auth::resolved(function (AuthManager $manager){
            $manager->provider('kratos', fn ($app, $config) => new KratosUserProvider($config['model']));
            $manager->provider('kratos-database', fn ($app, $config) => new KratosDatabaseUserProvider($config['model']));
            $manager->extend(
                'kratos',
                fn ($app, $name, array $config) => tap($this->createGuard($manager, $config), function ($guard) {
                    app()->refresh('request', $guard, 'setRequest');
                })
            );
        });
    }

    protected function createGuard(AuthManager $manager, array $config): RequestGuard
    {
        return new RequestGuard(
            app(Guard::class),
            request(),
            $manager->createUserProvider($config['provider'])
        );
    }
}