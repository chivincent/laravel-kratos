<?php

declare(strict_types=1);

namespace Chivincent\LaravelKratos\UserProviders;

use BadMethodCallException;
use InvalidArgumentException;
use Ory\Kratos\Client\Model\Identity;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Chivincent\LaravelKratos\Contracts\KratosIdentityContract;

class KratosUserProvider implements UserProvider
{
    public function __construct(protected string $model)
    {
    }

    public function retrieveById($identifier)
    {
        if (! $identifier instanceof Identity) {
            return null;
        }

        if (is_subclass_of($this->model, KratosIdentityContract::class)) {
            return $this->model::fromKratosIdentity($identifier);
        }

        throw new InvalidArgumentException(sprintf(
            '[%s] has not implemented [%s]',
            $this->model,
            KratosIdentityContract::class,
        ));
    }

    public function retrieveByToken($identifier, $token)
    {
        throw new BadMethodCallException('Unexpected method ['.__FUNCTION__.'] call');
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        throw new BadMethodCallException('Unexpected method ['.__FUNCTION__.'] call');
    }

    public function retrieveByCredentials(array $credentials)
    {
        throw new BadMethodCallException('Unexpected method ['.__FUNCTION__.'] call');
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        throw new BadMethodCallException('Unexpected method ['.__FUNCTION__.'] call');
    }
}
