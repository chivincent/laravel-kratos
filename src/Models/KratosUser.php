<?php

namespace Chivincent\LaravelKratos\Models;

use BadMethodCallException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Contracts\Auth\Authenticatable;
use Chivincent\LaravelKratos\Notifications\MustVerifyEmail;

class KratosUser extends Model implements Authenticatable
{
    use MustVerifyEmail;

    protected $table = 'identities';

    protected $hidden = [
        'nid',
        'metadata_admin',
    ];

    protected $casts = [
        'id' => 'string',
        'traits' => 'object',
    ];

    public function getConnectionName()
    {
        return config('kratos.user_providers.kratos-database.connection', 'kratos');
    }

    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function getAuthIdentifier()
    {
        return $this->id;
    }

    public function getAuthPassword()
    {
        throw new BadMethodCallException('Unexpected method ['.__FUNCTION__.'] call');
    }

    public function getRememberToken()
    {
        throw new BadMethodCallException('Unexpected method ['.__FUNCTION__.'] call');
    }

    public function setRememberToken($value)
    {
        throw new BadMethodCallException('Unexpected method ['.__FUNCTION__.'] call');
    }

    public function getRememberTokenName()
    {
        throw new BadMethodCallException('Unexpected method ['.__FUNCTION__.'] call');
    }

    public function hasVerifiedEmail()
    {
        return static::select('identity_verifiable_addresses.verified')
            ->leftJoin(
                'identity_verifiable_addresses',
                fn (JoinClause $join) => $join->on('identities.id', 'identity_verifiable_addresses.identity_id'),
            )
            ->where('identity_verifiable_addresses.verified', true)
            ->exists();
    }
}
