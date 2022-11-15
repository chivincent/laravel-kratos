<?php

declare(strict_types=1);

namespace Chivincent\LaravelKratos\UserProvider;

use BadMethodCallException;
use Chivincent\LaravelKratos\Contract\KratosIdentityContract;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use InvalidArgumentException;

class KratosUserProvider implements UserProvider
{
    public function __construct(protected string $model)
    {
    }

    public function retrieveById($identifier)
    {
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