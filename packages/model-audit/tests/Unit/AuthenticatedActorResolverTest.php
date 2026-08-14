<?php

namespace Local\ModelAudit\Tests\Unit;

use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Foundation\Auth\User;
use Local\ModelAudit\Resolvers\AuthenticatedActorResolver;
use Local\ModelAudit\Tests\TestCase;
use Mockery;

class AuthenticatedActorResolverTest extends TestCase
{
    public function test_it_returns_authenticated_actor(): void
    {
        $actor = new User;

        $guard = Mockery::mock(Guard::class);
        $guard->shouldReceive('user')
            ->once()
            ->andReturn($actor);

        $auth = Mockery::mock(AuthFactory::class);
        $auth->shouldReceive('guard')
            ->once()
            ->andReturn($guard);

        $resolver = new AuthenticatedActorResolver($auth);

        $this->assertSame($actor, $resolver->resolve());
    }

    public function test_it_returns_null_when_there_is_no_authenticated_actor(): void
    {
        $guard = Mockery::mock(Guard::class);
        $guard->shouldReceive('user')
            ->once()
            ->andReturnNull();

        $auth = Mockery::mock(AuthFactory::class);
        $auth->shouldReceive('guard')
            ->once()
            ->andReturn($guard);

        $resolver = new AuthenticatedActorResolver($auth);

        $this->assertNull($resolver->resolve());
    }
}
