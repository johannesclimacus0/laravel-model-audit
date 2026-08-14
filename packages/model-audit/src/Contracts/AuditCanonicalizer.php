<?php

namespace Local\ModelAudit\Contracts;

interface AuditCanonicalizer
{
    public function canonicalize(array $payload): string;
}
