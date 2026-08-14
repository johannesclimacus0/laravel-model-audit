<?php

namespace Local\ModelAudit\Tests\Unit;

use Local\ModelAudit\Resolvers\UuidRequestIdResolver;
use Illuminate\Support\Str;
use Local\ModelAudit\Tests\TestCase;

class UuidRequestIdResolverTest extends TestCase
{
    public function test_it_generates_a_uuid(): void
    {
        $uuid = new UuidRequestIdResolver()->resolve();
        $this->assertTrue(Str::isUuid($uuid));
    }
    public function test_it_returns_the_same_uuid_on_repeated_calls(): void
    {
        $resolver = new UuidRequestIdResolver();

        $first = $resolver->resolve();
        $second = $resolver->resolve();

        $this->assertSame($first, $second);
    }
}
