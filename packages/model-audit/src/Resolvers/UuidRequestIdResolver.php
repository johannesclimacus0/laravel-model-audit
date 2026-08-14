<?php

namespace Local\ModelAudit\Resolvers;

use Local\ModelAudit\Contracts\RequestIdResolver;
use Illuminate\Support\Str;

class UuidRequestIdResolver implements RequestIdResolver
{
    private ?string $requestId = null;

    public function resolve(): ?string
    {
        return $this->requestId ??= Str::uuid()->toString();
    }
}
