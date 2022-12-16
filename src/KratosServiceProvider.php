<?php

declare(strict_types=1);

namespace Chivincent\LaravelKratos;

use GuzzleHttp\Client;
use Illuminate\Auth\AuthManager;
use Illuminate\Auth\RequestGuard;
use Illuminate\Support\Facades\Auth;
use Ory\Kratos\Client\Api\CourierApi;
use Ory\Kratos\Client\Api\IdentityApi;
use Ory\Kratos\Client\Api\MetadataApi;
use Ory\Kratos\Client\Configuration;
use Ory\Kratos\Client\Api\FrontendApi;
use Illuminate\Support\ServiceProvider;
use Chivincent\LaravelKratos\UserProviders\DatabaseUserProvider;
use Chivincent\LaravelKratos\UserProviders\IdentityUserProvider;

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

        $this->registerKratosApis();
    }

    protected function registerKratosApis()
    {
        $public = Configuration::getDefaultConfiguration()->setHost(config('kratos.endpoints.public'));
        $admin = Configuration::getDefaultConfiguration()->setHost(config('kratos.endpoints.admin'));
        if ($key = config('kratos.api_key')) {
            $admin->setApiKey('Authorization', $key);
        }

        $this->app->singleton(
            CourierApi::class,
            fn () => new CourierApi(
                new Client(config('kratos.client_options')), $admin
            )
        );
        $this->app->singleton(
            FrontendApi::class,
            fn () => new FrontendApi(
                new Client(config('kratos.client_options')), $public
            )
        );
        $this->app->singleton(
            IdentityApi::class,
            fn () => new IdentityApi(
                new Client(config('kratos.client_options')), $admin
            )
        );
        $this->app->singleton(
            MetadataApi::class,
            fn () => new MetadataApi(
                new Client(config('kratos.client_options')), $public
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
        Auth::resolved(function (AuthManager $manager) {
            $manager->provider('kratos', fn ($app, $config) => new IdentityUserProvider($config['model']));
            $manager->provider('kratos-database', fn ($app, $config) => new DatabaseUserProvider($config['model']));
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
