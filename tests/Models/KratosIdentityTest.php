<?php

declare(strict_types=1);

namespace Tests\Models;

use DateTime;
use Tests\TestCase;
use Ory\Kratos\Client\Model\Identity;
use Chivincent\LaravelKratos\Models\KratosIdentity;

class KratosIdentityTest extends TestCase
{
    public function test_constructable()
    {
        $this->assertInstanceOf(
            KratosIdentity::class,
            new KratosIdentity(
                id: uuid_create(),
                schemaId:'default',
                schemaUrl: 'http://127.0.0.1:4433/schemas/ZGVmYXVsdA',
                state: 'active',
                stateChangedAt: now(),
                traits: (object) ['name' => [], 'email' => 'foo@bar.com'],
                verifiableAddresses: [],
                recoveryAddresses: [],
                metadataPublic: null,
                createdAt: now(),
                updatedAt: now(),
            )
        );
    }

    public function test_from_kratos_identity()
    {
        $kratosIdentity = new Identity([
            'id' => uuid_create(),
            'schemaId' => 'default',
            'schemaUrl' => 'http://127.0.0.1:4433/schemas/ZGVmYXVsdA',
            'state' => 'active',
            'stateChangedAt' => new DateTime(),
            'traits' => (object) ['name' => [], 'email' => 'foo@bar.com'],
            'verifiableAddresses' => [],
            'recoveryAddresses' => [],
            'metadataPublic' =>  null,
            'createdAt' => new DateTime(),
            'updatedAt' => new DateTime(),
        ]);

        $identity = KratosIdentity::fromKratosIdentity($kratosIdentity);
        $this->assertInstanceOf(KratosIdentity::class, $identity);
        $this->assertSame($kratosIdentity->getId(), $identity->id);
        $this->assertSame($kratosIdentity->getSchemaId(), $identity->schemaId);
        $this->assertSame($kratosIdentity->getSchemaUrl(), $identity->schemaUrl);
        $this->assertSame($kratosIdentity->getState(), $identity->state);
        $this->assertTrue($identity->stateChangedAt->eq($kratosIdentity->getStateChangedAt()));
        $this->assertSame($kratosIdentity->getTraits(), $identity->traits);
        $this->assertSame($kratosIdentity->getVerifiableAddresses(), $identity->verifiableAddresses);
        $this->assertSame($kratosIdentity->getRecoveryAddresses(), $identity->recoveryAddresses);
        $this->assertSame($kratosIdentity->getMetadataPublic(), $identity->metadataPublic);
        $this->assertTrue($identity->createdAt->eq($kratosIdentity->getCreatedAt()));
        $this->assertTrue($identity->updatedAt->eq($kratosIdentity->getUpdatedAt()));
    }

    public function test_from_kratos_identity_with_null_columns()
    {
        $identity = KratosIdentity::fromKratosIdentity(new Identity([
            'id' => uuid_create(),
            'schemaId' => 'default',
            'schemaUrl' => 'http://127.0.0.1:4433/schemas/ZGVmYXVsdA',
        ]));

        $this->assertNull($identity->state);
        $this->assertNull($identity->stateChangedAt);
        $this->assertNull($identity->traits);
        $this->assertNull($identity->verifiableAddresses);
        $this->assertNull($identity->recoveryAddresses);
        $this->assertNull($identity->metadataPublic);
        $this->assertNull($identity->createdAt);
        $this->assertNull($identity->updatedAt);
    }

    public function test_serializable()
    {
        $identity = new KratosIdentity(
            id: uuid_create(),
            schemaId:'default',
            schemaUrl: 'http://127.0.0.1:4433/schemas/ZGVmYXVsdA',
            state: 'active',
            stateChangedAt: now(),
            traits: (object) ['name' => [], 'email' => 'foo@bar.com'],
            verifiableAddresses: [],
            recoveryAddresses: [],
            metadataPublic: null,
            createdAt: now(),
            updatedAt: now(),
        );

        $this->assertIsArray($identity->toArray());
        $this->assertJson($identity->toJson());
        $this->assertJson(json_encode($identity));
        $this->assertJson((string) $identity);
        $this->assertJsonStringEqualsJsonString($identity->toJson(), json_encode($identity));
    }
}
