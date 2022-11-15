<?php

return [
    'endpoints' => [
        'public' => env('KRATOS_PUBLIC_ENDPOINT', 'http://127.0.0.1:4433'),
    ],

    'client_options' => [
        'connect_timeout' => 5,
        'debug' => false,
    ],

    'user_providers' => [
        'kratos' => [
            'driver' => 'kratos',
            'model' => Chivincent\LaravelKratos\Models\KratosIdentity::class,
        ],

        'kratos-database' => [
            'driver' => 'kratos-database',
            'model' => App\Models\User::class,
        ],
    ],
];