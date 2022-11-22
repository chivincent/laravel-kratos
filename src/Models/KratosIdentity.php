<?php

declare(strict_types=1);

namespace Chivincent\LaravelKratos\Models;

use DateTime;
use BadMethodCallException;
use Illuminate\Support\Carbon;
use Ory\Kratos\Client\Model\Identity;
use Illuminate\Contracts\Auth\Authenticatable;
use Chivincent\LaravelKratos\Contracts\KratosIdentityContract;

class KratosIdentity implements KratosIdentityContract, Authenticatable
{
    public function __construct(
        public string $id,
        public string $schemaId,
        public string $schemaUrl,
        public ?string $state,
        public ?Carbon $stateChangedAt,
        public ?object $traits,
        public ?array $verifiableAddresses,
        public ?array $recoveryAddresses,
        public ?object $metadataPublic,
        public ?Carbon $createdAt,
        public ?Carbon $updatedAt,
    ) {
    }

    public static function fromKratosIdentity(Identity $identity): static
    {
        return new static(
            $identity->getId(),
            $identity->getSchemaId(),
            $identity->getSchemaUrl(),
            $identity->getState(),
            $identity->getStateChangedAt() ? new Carbon($identity->getStateChangedAt()) : null,
            $identity->getTraits(),
            $identity->getVerifiableAddresses(),
            $identity->getRecoveryAddresses(),
            $identity->getMetadataPublic(),
            $identity->getCreatedAt() ? new Carbon($identity->getCreatedAt()) : null,
            $identity->getUpdatedAt() ? new Carbon($identity->getUpdatedAt()) : null,
        );
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
}
