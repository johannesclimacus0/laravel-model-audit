<?php

namespace Johannesclimacus\ModelAudit\Contracts;

interface AuditCanonicalizer
{
    public function canonicalize(array $payload): string;
}
