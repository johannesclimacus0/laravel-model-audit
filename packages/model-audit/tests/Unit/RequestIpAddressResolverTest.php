<?php

namespace Local\ModelAudit\Tests\Unit;

use Illuminate\Http\Request;
use Local\ModelAudit\Resolvers\RequestIpAddressResolver;
use Local\ModelAudit\Tests\TestCase;

class RequestIpAddressResolverTest extends TestCase
{
    public function test_it_resolves_the_request_ip_address(): void
    {
        $request = Request::create('/','GET', server: ['REMOTE_ADDR'=>'203.0.113.10']);

        $resolver = new RequestIpAddressResolver($request);

        $this->assertSame('203.0.113.10', $resolver->resolve());
    }
}
