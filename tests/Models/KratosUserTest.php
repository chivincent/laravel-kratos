<?php

namespace Tests\Models;

use Chivincent\LaravelKratos\Models\KratosUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KratosUserTest extends TestCase
{
    use RefreshDatabase;

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    public function test_retrieve_from_database()
    {
        (new KratosUser())->forceFill([
            'id' => $id = uuid_create(),
            'schema_id' => 'default',
            'traits' => (object) ['name' => [], 'email' => 'foo@bar.com'],
        ])->save();

        $user = KratosUser::find($id);

        $this->assertInstanceOf(KratosUser::class, $user);
        $this->assertSame($id, $user->getAuthIdentifier());
        $this->assertSame('id', $user->getAuthIdentifierName());
    }
}