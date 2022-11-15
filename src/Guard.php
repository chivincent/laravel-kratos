<?php

declare(strict_types=1);


namespace Chivincent\LaravelKratos;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Ory\Kratos\Client\ApiException;
use Ory\Kratos\Client\Model\Session;
use Ory\Kratos\Client\Api\V0alpha2Api as KratosApi;

class Guard
{
    protected const KRATOS_SESSION_COOKIE = 'ory_kratos_session';

    public function __construct(protected KratosApi $api)
    {
    }

    public function __invoke(Request $request, UserProvider $provider): ?Authenticatable
    {
        $session = $this->getKratosSession($request->cookie(static::KRATOS_SESSION_COOKIE));
        if (! ($identity = $session?->getIdentity())) {
            return null;
        }

        return $provider->retrieveById($identity);
    }

    protected function getKratosSession(?string $cookie): ?Session
    {
        if (! $cookie) {
            return null;
        }

        try {
            $session = $this->api->toSession(cookie: static::KRATOS_SESSION_COOKIE."=$cookie");
        } catch (ApiException) {
            return null;
        }

        return $session instanceof Session
            ? $session : null;
    }
}