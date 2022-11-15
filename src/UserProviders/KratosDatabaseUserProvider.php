<?php

declare(strict_types=1);

namespace Chivincent\LaravelKratos\UserProvider;

use BadMethodCallException;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class KratosDatabaseUserProvider extends EloquentUserProvider
{
    public function __construct($model)
    {
        parent::__construct(app('hash'), $model);
    }

    public function retrieveById($identifier)
    {
        if (! ($id = $identifier?->getId())) {
            return null;
        }

        $model = $this->createModel();

        return $this->newModelQuery($model)
            ->where($model->getAuthIdentifierName(), $id)
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