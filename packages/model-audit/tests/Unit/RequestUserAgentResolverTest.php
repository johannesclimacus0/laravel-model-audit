<?php

namespace Johannesclimacus\ModelAudit\Tests\Unit;

use Illuminate\Http\Request;
use Johannesclimacus\ModelAudit\Resolvers\RequestUserAgentResolver;
use Johannesclimacus\ModelAudit\Tests\TestCase;

class RequestUserAgentResolverTest extends TestCase
{
    public function test_it_resolves_the_request_user_agent(): void
    {
        $request = Request::create('/', 'GET', server: ['HTTP_USER_AGENT' => 'Chrome']);
        $resolver = new RequestUserAgentResolver($request);

        $this->assertSame('Chrome', $resolver->resolve());
    }

    public function test_it_returns_null_when_the_user_agent_is_missing(): void
    {
        $request = Request::create('/', 'GET', server: ['HTTP_USER_AGENT' => null]);
        $resolver = new RequestUserAgentResolver($request);

        $this->assertNull($resolver->resolve());
    }
}
