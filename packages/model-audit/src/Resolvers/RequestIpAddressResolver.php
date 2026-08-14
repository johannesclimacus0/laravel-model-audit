<?php

namespace Local\ModelAudit\Resolvers;

use Illuminate\Http\Request;
use Local\ModelAudit\Contracts\IpAddressResolver;

class RequestIpAddressResolver implements IpAddressResolver
{
    public function __construct(private Request $request) {}

    public function resolve(): ?string
    {
        return $this->request->ip();
    }
}
