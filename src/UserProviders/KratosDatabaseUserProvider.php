<?php

declare(strict_types=1);

namespace Chivincent\LaravelKratos\UserProviders;

use BadMethodCallException;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Ory\Kratos\Client\Model\Identity;

class KratosDatabaseUserProvider extends EloquentUserProvider
{
    public function __construct($model)
    {
        parent::__construct(app('hash'), $model);
    }

    public function retrieveById($identifier)
    {
        if (! $identifier instanceof Identity) {
            return null;
        }

        $model = $this->createModel();

        return $this->newModelQuery($model)
            ->where($model->getAuthIdentifierName(), $identifier->getId())
            ->first();
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