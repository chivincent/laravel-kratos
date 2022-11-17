<?php

declare(strict_types=1);

namespace Tests;

use Chivincent\LaravelKratos\KratosServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [KratosServiceProvider::class];
    }

    protected function defineEnvironment($app)
    {
        $app['config']->set('kratos.client_options', [
            'debug' => true,
            'timeout' => 0
        ]);
    }
}
