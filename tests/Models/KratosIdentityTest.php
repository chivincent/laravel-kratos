<?php

declare(strict_types=1);

namespace Tests\Models;

use DateTime;
use Tests\TestCase;
use Ory\Kratos\Client\Model\Identity;
use Chivincent\LaravelKratos\Models\KratosIdentity;
use Ory\Kratos\Client\Model\VerifiableIdentityAddress;

class KratosIdentityTest extends TestCase
{
    /**
     * @dataProvider identityProvider
     */
    public function test_constructable(KratosIdentity $identity)
    {
        $this->assertInstanceOf(KratosIdentity::class, $identity);
    }

    /**
     * @dataProvider kratosIdentityProvider
     */
    public function test_from_kratos_identity(Identity $kratosIdentity)
    {
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

    /**
     * @dataProvider identityProvider
     */
    public function test_serializable(KratosIdentity $identity)
    {
        $this->assertIsArray($identity->toArray());
        $this->assertJson($identity->toJson());
        $this->assertJson(json_encode($identity));
        $this->assertJson((string) $identity);
        $this->assertJsonStringEqualsJsonString($identity->toJson(), json_encode($identity));
    }

    /**
     * @dataProvider identityProvider
     */
    public function test_has_verified_email_as_verifiable_addresses_not_set(KratosIdentity $identity)
    {
        $this->assertFalse($identity->hasVerifiedEmail());
    }

    /**
     * @dataProvider unverifiedIdentityProvider
     */
    public function test_has_verified_email_as_unverified(KratosIdentity $identity)
    {
        $this->assertFalse($identity->hasVerifiedEmail());
    }

    /**
     * @dataProvider verifiedIdentityProvider
     */
    public function test_has_verified_email_as_verified(KratosIdentity $identity)
    {
        $this->assertTrue($identity->hasVerifiedEmail());
    }

    public function identityProvider(): array
    {
        return [
             [
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
                ),
            ]
        ];
    }

    public function kratosIdentityProvider(): array
    {
        return [
            [
                new Identity([
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
                ])
            ]
        ];
    }

    public function unverifiedIdentityProvider(): array
    {
        return [
            'single_verifiable_addresses' => [
                new KratosIdentity(
                    id: uuid_create(),
                    schemaId:'default',
                    schemaUrl: 'http://127.0.0.1:4433/schemas/ZGVmYXVsdA',
                    state: 'active',
                    stateChangedAt: now(),
                    traits: (object) ['name' => [], 'email' => 'foo@bar.com'],
                    verifiableAddresses: [new VerifiableIdentityAddress([
                        'id' => uuid_create(),
                        'status' => 'sent',
                        'via' => 'email',
                        'verified' => false,
                        'value' => 'foo@baz.test',
                        'verified_at' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ])],
                    recoveryAddresses: [],
                    metadataPublic: null,
                    createdAt: now(),
                    updatedAt: now(),
                )
            ],
            'multiple_verifiable_addresses' => [
                new KratosIdentity(
                    id: uuid_create(),
                    schemaId:'default',
                    schemaUrl: 'http://127.0.0.1:4433/schemas/ZGVmYXVsdA',
                    state: 'active',
                    stateChangedAt: now(),
                    traits: (object) ['name' => [], 'email' => 'foo@bar.com'],
                    verifiableAddresses: [
                        new VerifiableIdentityAddress([
                        'id' => uuid_create(),
                        'status' => 'sent',
                        'via' => 'email',
                        'verified' => false,
                        'value' => 'foo@baz.test',
                        'verified_at' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]),
                        new VerifiableIdentityAddress([
                            'id' => uuid_create(),
                            'status' => 'sent',
                            'via' => 'email',
                            'verified' => false,
                            'value' => 'bar@baz.test',
                            'verified_at' => null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]),
                    ],
                    recoveryAddresses: [],
                    metadataPublic: null,
                    createdAt: now(),
                    updatedAt: now(),
                )
            ]
        ];
    }

    public function verifiedIdentityProvider(): array
    {
        return [
            'single_verifiable_addresses' => [
                new KratosIdentity(
                    id: uuid_create(),
                    schemaId:'default',
                    schemaUrl: 'http://127.0.0.1:4433/schemas/ZGVmYXVsdA',
                    state: 'active',
                    stateChangedAt: now(),
                    traits: (object) ['name' => [], 'email' => 'foo@bar.com'],
                    verifiableAddresses: [new VerifiableIdentityAddress([
                        'id' => uuid_create(),
                        'status' => 'completed',
                        'via' => 'email',
                        'verified' => true,
                        'value' => 'foo@baz.test',
                        'verified_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ])],
                    recoveryAddresses: [],
                    metadataPublic: null,
                    createdAt: now(),
                    updatedAt: now(),
                )
            ],
            'multiple_verifiable_addresses' => [
                new KratosIdentity(
                    id: uuid_create(),
                    schemaId:'default',
                    schemaUrl: 'http://127.0.0.1:4433/schemas/ZGVmYXVsdA',
                    state: 'active',
                    stateChangedAt: now(),
                    traits: (object) ['name' => [], 'email' => 'foo@bar.com'],
                    verifiableAddresses: [
                        new VerifiableIdentityAddress([
                            'id' => uuid_create(),
                            'status' => 'sent',
                            'via' => 'email',
                            'verified' => false,
                            'value' => 'foo@baz.test',
                            'verified_at' => null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]),
                        new VerifiableIdentityAddress([
                            'id' => uuid_create(),
                            'status' => 'completed',
                            'via' => 'email',
                            'verified' => true,
                            'value' => 'bar@baz.test',
                            'verified_at' => now(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]),
                    ],
                    recoveryAddresses: [],
                    metadataPublic: null,
                    createdAt: now(),
                    updatedAt: now(),
                )
            ]
        ];
    }
}
