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
        $user = KratosUser::find($id = $this->createKratosUser());

        $this->assertInstanceOf(KratosUser::class, $user);
        $this->assertSame($id, $user->getAuthIdentifier());
        $this->assertSame('id', $user->getAuthIdentifierName());
    }

    public function test_has_verified_email_as_verifiable_addresses_not_set()
    {
        $user = KratosUser::find($this->createKratosUser());

        $this->assertFalse($user->hasVerifiedEmail());
    }

    public function test_has_verified_email_as_unverified()
    {
        $id = $this->createKratosUser();
        $this->createVerifiableAddresses(['identity_id' => $id, 'verified' => false]);

        $user = KratosUser::find($id);

        $this->assertFalse($user->hasVerifiedEmail());
    }

    public function test_has_verified_email_as_verified()
    {
        $id = $this->createKratosUser();
        $this->createVerifiableAddresses(['identity_id' => $id, 'verified' => true]);

        $user = KratosUser::find($id);

        $this->assertTrue($user->hasVerifiedEmail());
    }

    public function test_has_verified_email_as_multiple_unverified_addresses()
    {
        $id = $this->createKratosUser();
        $this->createVerifiableAddresses(['identity_id' => $id, 'verified' => false]);
        $this->createVerifiableAddresses(['identity_id' => $id, 'verified' => false]);

        $user = KratosUser::find($id);

        $this->assertFalse($user->hasVerifiedEmail());
    }

    public function test_has_verified_email_as_multiple_verified_addresses()
    {
        $id = $this->createKratosUser();
        $this->createVerifiableAddresses(['identity_id' => $id, 'verified' => false]);
        $this->createVerifiableAddresses(['identity_id' => $id, 'verified' => true]);

        $user = KratosUser::find($id);

        $this->assertTrue($user->hasVerifiedEmail());
    }

    protected function createKratosUser(): string
    {
        (new KratosUser())
            ->forceFill([
                'id' => $id = uuid_create(),
                'schema_id' => 'default',
                'traits' => (object)['name' => [], 'email' => 'foo@bar.com'],
            ])
            ->save();

        return $id;
    }

    protected function createVerifiableAddresses(array $attributes): void
    {
        DB::insert('INSERT INTO identity_verifiable_addresses VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
            $attributes['id'] ?? uuid_create(),
            $attributes['status'] ?? ($attributes['verified'] ? 'completed' : 'sent'),
            $attributes['via'] ?? 'email',
            $attributes['verified'],
            $attributes['value'] ?? 'foo@bar.test',
            $attributes['verified_at'] ?? ($attributes['verified'] ? now() : null),
            $attributes['identity_id'],
            $attributes['created_at'] ?? now(),
            $attributes['updated_at'] ?? now(),
            $attributes['nid'] ?? uuid_create(),
        ]);
    }
}
