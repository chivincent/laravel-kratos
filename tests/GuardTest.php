<?php

namespace Tests;

use Chivincent\LaravelKratos\Guard;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Mockery;
use Ory\Kratos\Client\Api\V0alpha2Api as KratosApi;
use Ory\Kratos\Client\ApiException;
use Ory\Kratos\Client\Model\Identity;
use Ory\Kratos\Client\Model\Session;
use stdClass;

class GuardTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        Mockery::close();
    }

    public function test_constructable()
    {
        $this->assertInstanceOf(Guard::class, app(Guard::class));
    }

    public function test_authentication_with_kratos_session()
    {
        $identity = Mockery::mock(Identity::class);

        $session = Mockery::mock(Session::class);
        $session->shouldReceive('getIdentity')->andReturn($identity);

        $this->mock(KratosApi::class)
            ->shouldReceive('toSession')
            ->with(null, 'ory_kratos_session=foobar')
            ->andReturn($session);

        $provider = Mockery::mock(UserProvider::class);
        $provider->shouldReceive('retrieveById')
            ->with($identity)
            ->andReturn(Mockery::mock(Authenticatable::class));

        $user = app(Guard::class)(
            Request::create('/', cookies: ['ory_kratos_session' => 'foobar']),
            $provider
        );

        $this->assertInstanceOf(Authenticatable::class, $user);
    }

    public function test_authentication_failed_without_kratos_session()
    {
        $user = app(Guard::class)(
            Request::create('/'),
            Mockery::mock(UserProvider::class)
        );

        $this->assertNull($user);
    }

    public function test_authentication_failed_with_invalid_kratos_session()
    {
        $this->mock(KratosApi::class)
            ->shouldReceive('toSession')
            ->with(null, 'ory_kratos_session=foobar')
            ->andReturn('invalid_session');

        $user = app(Guard::class)(
            Request::create('/', cookies: ['ory_kratos_session' => 'foobar']),
            Mockery::mock(UserProvider::class)
        );

        $this->assertNull($user);
    }

    public function test_authentication_failed_with_exception_of_kratos_sdk()
    {
        $this->mock(KratosApi::class)
            ->shouldReceive('toSession')
            ->with(null, 'ory_kratos_session=foobar')
            ->andThrow(ApiException::class);

        $user = app(Guard::class)(
            Request::create('/', cookies: ['ory_kratos_session' => 'foobar']),
            Mockery::mock(UserProvider::class),
        );

        $this->assertNull($user);
    }

    public function test_authentication_failed_with_cannot_get_identity_from_session()
    {
        $session = Mockery::mock(Session::class);
        $session->shouldReceive('getIdentity')->andReturn(null);

        $this->mock(KratosApi::class)
            ->shouldReceive('toSession')
            ->with(null, 'ory_kratos_session=foobar')
            ->andReturn($session);

        $user = app(Guard::class)(
            Request::create('/', cookies: ['ory_kratos_session' => 'foobar']),
            Mockery::mock(UserProvider::class),
        );

        $this->assertNull($user);
    }
}