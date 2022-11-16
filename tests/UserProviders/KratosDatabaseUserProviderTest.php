<?php

namespace Tests\UserProviders;

use Chivincent\LaravelKratos\Models\KratosUser;
use Chivincent\LaravelKratos\UserProviders\KratosDatabaseUserProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Ory\Kratos\Client\Model\Identity;
use Tests\TestCase;

class KratosDatabaseUserProviderTest extends TestCase
{
    use RefreshDatabase;

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    public function test_constructable()
    {
        $this->assertInstanceOf(KratosDatabaseUserProvider::class, new KratosDatabaseUserProvider(KratosUser::class));
    }

    public function test_retrieve_by_id()
    {
        $provider = new KratosDatabaseUserProvider(KratosUser::class);

        $kratosIdentity = Mockery::mock(Identity::class);
        $kratosIdentity->shouldReceive('getId')->once()->andReturn($id = uuid_create());
        (new KratosUser())->forceFill([
            'id' => $id,
            'schema_id' => 'default',
            'traits' => (object) ['name' => [], 'email' => 'foo@bar.com'],
        ])->save();

        $user = $provider->retrieveById($kratosIdentity);

        $this->assertSame($user->getAuthIdentifier(), $id);
    }

    public function test_retrieve_by_id_with_invalid_identifier()
    {
        $provider = new KratosDatabaseUserProvider(KratosUser::class);

        $this->assertNull($provider->retrieveById('invalid_identifier'));
    }
}