<?php

namespace Johannesclimacus\ModelAudit\Resolvers;

use Illuminate\Support\Str;
use Johannesclimacus\ModelAudit\Contracts\RequestIdResolver;

class UuidRequestIdResolver implements RequestIdResolver
{
    private ?string $requestId = null;

    public function resolve(): ?string
    {
        return $this->requestId ??= Str::uuid()->toString();
    }
}
