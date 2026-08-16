<?php

namespace Johannesclimacus\ModelAudit\Resolvers;

use Illuminate\Http\Request;
use Johannesclimacus\ModelAudit\Contracts\IpAddressResolver;

class RequestIpAddressResolver implements IpAddressResolver
{
    public function __construct(private Request $request) {}

    public function resolve(): ?string
    {
        return $this->request->ip();
    }
}
