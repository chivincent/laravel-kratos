<?php

namespace Chivincent\LaravelKratos\Models;

use BadMethodCallException;
use Chivincent\LaravelKratos\Notifications\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;

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
        // TODO
    }
}
