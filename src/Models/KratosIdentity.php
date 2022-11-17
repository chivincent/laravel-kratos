<?php

declare(strict_types=1);

namespace Chivincent\LaravelKratos\Models;

use DateTime;
use BadMethodCallException;
use Ory\Kratos\Client\Model\Identity;
use Illuminate\Contracts\Auth\Authenticatable;
use Chivincent\LaravelKratos\Contracts\KratosIdentityContract;

class KratosIdentity implements KratosIdentityContract, Authenticatable
{
    public function __construct(
        public string $id,
        public string $schemaId,
        public string $schemaUrl,
        public string $state,
        public DateTime $stateChangedAt,
        public object $traits,
        public array $verifiableAddresses,
        public array $recoveryAddresses,
        public ?object $metadataPublic,
        public DateTime $createdAt,
        public DateTime $updatedAt,
    ) {
    }

    public static function fromKratosIdentity(Identity $identity): static
    {
        return new static(
            $identity->getId(),
            $identity->getSchemaId(),
            $identity->getSchemaUrl(),
            $identity->getState(),
            $identity->getStateChangedAt(),
            $identity->getTraits(),
            $identity->getVerifiableAddresses(),
            $identity->getRecoveryAddresses(),
            $identity->getMetadataPublic(),
            $identity->getCreatedAt(),
            $identity->getUpdatedAt(),
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
