# Laravel Kratos

## Introduction
---

Laravel Kratos integrates authentication features with [ory/kratos](https://github.com/ory/kratos).

## Requirements
---

- I'm  building an API with Laravel.
- I'm not using built-in authentication feature in Laravel, because Kratos has been implemented.
  - I won't use them also in the future.
- The frontend is a separated project
  - The frontend authenticates with Kratos server, and receive a `ory_kratos_session` cookie.
  - The frontend makes requests to the Laravel API, with `ory_kratos_session` cookie.

## Install
---

```shell
composer require chivincent/laravel-kratos
```

## Configuration

```shell
php artisan vendor:publish --provider="Chivincent\LaravelKratos\KratosServiceProvider"
```

- Check the `config/kratos.php`, ensure the endpoint of Kratos service.

- Update `config/auth.php`

```php
<?php

return [
    // ...
    'guards' => [
        'kratos' => [
            'driver' => 'kratos',
            'provider' => 'kratos', // or 'kratos-database'
        ],    
    ],
    // ...
];
```

- Update `config/cors.php`

```php
<?php

return [
    // ...
    
    'allowed_origins' => ['http://127.0.0.1:4455'], // Port 4455 is the default application of Kratos Frontend UI
    
    // ...
    
    'supports_credentials' => true,
    
    // ...
]; 
```

### Database Configuration

If using `kratos-database` as UserProvider in `auth.guards.kratos.provider`, it's helpful to setup connection with default user model.

- Update `config/database.php`, it is an example for Postgresql below:

```php
<?php

return [
    // ...
    'connections' => [
        'kratos' => [ // connection name should as same as `config('kratos.user_providers.kratos-database.connection')` 
            'driver' => 'pgsql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_KRATOS_DATABASE', 'kratos'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'prefer',
        ],    
    ]
    // ... 
];
```

## Usage

In `routes/api.php`:

```php
Route::middleware('auth:kratos')
    ->get('user', fn (Request $request) => response()->json($request->user()));
```
