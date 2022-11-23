<?php

declare(strict_types=1);

namespace Chivincent\LaravelKratos\Models;

use Stringable;
use JsonSerializable;
use BadMethodCallException;
use Illuminate\Support\Carbon;
use Ory\Kratos\Client\Model\Identity;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Auth\Authenticatable;
use Chivincent\LaravelKratos\Contracts\KratosIdentityContract;

class KratosIdentity implements KratosIdentityContract, Authenticatable, Arrayable, JsonSerializable, Jsonable, Stringable
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

    public function __toString(): string
    {
        return $this->toJson();
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

    public function toArray()
    {
        return [
            'id' => $this->id,
            'schema_id' => $this->schemaId,
            'schema_url' => $this->schemaUrl,
            'state' => $this->state,
            'state_changed_at' => $this->stateChangedAt,
            'traits' => $this->traits,
            'verifiable_addresses' => $this->verifiableAddresses,
            'recovery_addresses' => $this->recoveryAddresses,
            'metadata_public' => $this->metadataPublic,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toJson($options = JSON_THROW_ON_ERROR)
    {
        return json_encode($this->jsonSerialize(), $options);
    }
}
