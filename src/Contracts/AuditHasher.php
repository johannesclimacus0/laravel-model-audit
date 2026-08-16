<?php

namespace Johannesclimacus\ModelAudit\Contracts;

interface AuditHasher
{
    public function hash(string $value): string;
}
