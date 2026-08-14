<?php

namespace Local\ModelAudit\Tests\Feature;

use Local\ModelAudit\Contracts\ActorResolver;
use Local\ModelAudit\Resolvers\AuthenticatedActorResolver;
use Local\ModelAudit\Tests\TestCase;

class ModelAuditServiceProviderTest extends TestCase
{
    public function test_it_registers_the_default_actor_resolver(): void
    {
        $resolver = $this->app->make(ActorResolver::class);

        $this->assertInstanceOf(AuthenticatedActorResolver::class, $resolver);

        $this->assertSame($resolver, $this->app->make(ActorResolver::class));
    }
}
