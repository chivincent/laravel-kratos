<?php

namespace Tests\Models;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Chivincent\LaravelKratos\Models\KratosUser;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
            'traits' => (object)['name' => [], 'email' => 'foo@bar.com'],
        ])->save();

        $user = KratosUser::find($id);

        $this->assertInstanceOf(KratosUser::class, $user);
        $this->assertSame($id, $user->getAuthIdentifier());
        $this->assertSame('id', $user->getAuthIdentifierName());
    }

    public function test_has_verified_email_as_verifiable_addresses_not_set()
    {
        (new KratosUser())->forceFill([
            'id' => $id = uuid_create(),
            'schema_id' => 'default',
            'traits' => (object)['name' => [], 'email' => 'foo@bar.com'],
        ])->save();

        $user = KratosUser::find($id);

        $this->assertFalse($user->hasVerifiedEmail());
    }

    public function test_has_verified_email_as_unverified()
    {
        (new KratosUser())->forceFill([
            'id' => $id = uuid_create(),
            'schema_id' => 'default',
            'traits' => (object)['name' => [], 'email' => 'foo@bar.com'],
        ])->save();
        DB::insert('INSERT INTO identity_verifiable_addresses VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
            uuid_create(),
            'sent',
            'email',
            false,
            'foo@bar.test',
            null,
            $id,
            now(),
            now(),
            uuid_create(),
        ]);

        $user = KratosUser::find($id);

        $this->assertFalse($user->hasVerifiedEmail());
    }

    public function test_has_verified_email_as_verified()
    {
        (new KratosUser())->forceFill([
            'id' => $id = uuid_create(),
            'schema_id' => 'default',
            'traits' => (object)['name' => [], 'email' => 'foo@bar.com'],
        ])->save();
        DB::insert('INSERT INTO identity_verifiable_addresses VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
            uuid_create(),
            'completed',
            'email',
            true,
            'foo@bar.test',
            null,
            $id,
            now(),
            now(),
            uuid_create(),
        ]);

        $user = KratosUser::find($id);

        $this->assertTrue($user->hasVerifiedEmail());
    }

    public function test_has_verified_email_as_multiple_unverified_addresses()
    {
        (new KratosUser())->forceFill([
            'id' => $id = uuid_create(),
            'schema_id' => 'default',
            'traits' => (object)['name' => [], 'email' => 'foo@bar.com'],
        ])->save();
        DB::insert('INSERT INTO identity_verifiable_addresses VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
            uuid_create(),
            'sent',
            'email',
            false,
            'foo@bar.test',
            null,
            $id,
            now(),
            now(),
            uuid_create(),
        ]);
        DB::insert('INSERT INTO identity_verifiable_addresses VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
            uuid_create(),
            'sent',
            'email',
            false,
            'bar@bar.test',
            null,
            $id,
            now(),
            now(),
            uuid_create(),
        ]);

        $user = KratosUser::find($id);

        $this->assertFalse($user->hasVerifiedEmail());
    }

    public function test_has_verified_email_as_multiple_verified_addresses()
    {
        (new KratosUser())->forceFill([
            'id' => $id = uuid_create(),
            'schema_id' => 'default',
            'traits' => (object)['name' => [], 'email' => 'foo@bar.com'],
        ])->save();
        DB::insert('INSERT INTO identity_verifiable_addresses VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
            uuid_create(),
            'sent',
            'email',
            false,
            'foo@bar.test',
            null,
            $id,
            now(),
            now(),
            uuid_create(),
        ]);
        DB::insert('INSERT INTO identity_verifiable_addresses VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
            uuid_create(),
            'completed',
            'email',
            true,
            'bar@bar.test',
            null,
            $id,
            now(),
            now(),
            uuid_create(),
        ]);

        $user = KratosUser::find($id);

        $this->assertTrue($user->hasVerifiedEmail());
    }
}
