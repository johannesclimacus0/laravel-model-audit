<?php

namespace Local\ModelAudit\Tests\Feature;

use Local\ModelAudit\Contracts\ActorResolver;
use Local\ModelAudit\Contracts\IpAddressResolver;
use Local\ModelAudit\Contracts\RequestIdResolver;
use Local\ModelAudit\Contracts\UserAgentResolver;
use Local\ModelAudit\Resolvers\AuthenticatedActorResolver;
use Local\ModelAudit\Resolvers\RequestIpAddressResolver;
use Local\ModelAudit\Resolvers\RequestUserAgentResolver;
use Local\ModelAudit\Resolvers\UuidRequestIdResolver;
use Local\ModelAudit\Tests\TestCase;

class ModelAuditServiceProviderTest extends TestCase
{
    public function test_it_registers_the_default_actor_resolver(): void
    {
        $resolver = $this->app->make(ActorResolver::class);

        $this->assertInstanceOf(AuthenticatedActorResolver::class, $resolver);

        $this->assertSame($resolver, $this->app->make(ActorResolver::class));
    }

    public function test_it_registers_the_default_request_context_resolvers(): void
    {
        $resolver = $this->app->make(IpAddressResolver::class);
        $this->assertInstanceOf(RequestIpAddressResolver::class, $resolver);
        $this->assertSame($resolver, $this->app->make(IpAddressResolver::class));

        $resolver = $this->app->make(UserAgentResolver::class);
        $this->assertInstanceOf(RequestUserAgentResolver::class, $resolver);
        $this->assertSame($resolver, $this->app->make(UserAgentResolver::class));

        $resolver = $this->app->make(RequestIdResolver::class);
        $this->assertInstanceOf(UuidRequestIdResolver::class, $resolver);
        $this->assertSame($resolver, $this->app->make(RequestIdResolver::class));
    }
}
