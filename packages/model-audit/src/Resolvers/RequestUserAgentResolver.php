<?php

namespace Johannesclimacus\ModelAudit\Resolvers;

use Illuminate\Http\Request;
use Johannesclimacus\ModelAudit\Contracts\UserAgentResolver;

class RequestUserAgentResolver implements UserAgentResolver
{
    public function __construct(private Request $request) {}

    public function resolve(): ?string
    {
        return $this->request->userAgent() ?? null;
    }
}
