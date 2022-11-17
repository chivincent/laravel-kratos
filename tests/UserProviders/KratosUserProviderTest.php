<?php

namespace Tests\UserProviders;

use Mockery;
use DateTime;
use Tests\TestCase;
use InvalidArgumentException;
use Ory\Kratos\Client\Model\Identity;
use Chivincent\LaravelKratos\Models\KratosIdentity;
use Chivincent\LaravelKratos\UserProviders\KratosUserProvider;

class KratosUserProviderTest extends TestCase
{
    public function test_constructable()
    {
        $this->assertInstanceOf(KratosUserProvider::class, new KratosUserProvider(KratosIdentity::class));
    }

    public function test_retrieve_by_id()
    {
        $provider = new KratosUserProvider(KratosIdentity::class);

        $kratosIdentity = new Identity([
            'id' => $id = uuid_create(),
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

        $identity = $provider->retrieveById($kratosIdentity);

        $this->assertSame($identity->getAuthIdentifier(), $id);
    }

    public function test_retrieve_by_id_with_invalid_model()
    {
        $provider = new KratosUserProvider('foo');

        $this->expectException(InvalidArgumentException::class);
        $provider->retrieveById(Mockery::mock(Identity::class));
    }

    public function test_retrieve_by_id_with_invalid_identifier()
    {
        $provider = new KratosUserProvider(KratosIdentity::class);

        $this->assertNull($provider->retrieveById('invalid_identifier'));
    }
}
