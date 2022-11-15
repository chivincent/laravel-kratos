<?php

namespace Chivincent\LaravelKratos\Contract;

use Ory\Kratos\Client\Model\Identity;

interface KratosIdentityContract
{
    public static function fromKratosIdentity(Identity $identity): static;
}