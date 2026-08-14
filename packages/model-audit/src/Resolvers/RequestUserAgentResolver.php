<?php

namespace Local\ModelAudit\Resolvers;

use Illuminate\Http\Request;
use Local\ModelAudit\Contracts\UserAgentResolver;

class RequestUserAgentResolver implements UserAgentResolver
{
    public function __construct(private Request $request)
    {
    }

    public function resolve(): ?string
    {
        return $this->request->userAgent() ?? null;
    }
}
