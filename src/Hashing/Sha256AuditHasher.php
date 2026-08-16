<?php

namespace Johannesclimacus\ModelAudit\Hashing;

use Johannesclimacus\ModelAudit\Contracts\AuditHasher;

class Sha256AuditHasher implements AuditHasher
{
    public function hash(string $value): string
    {
        return hash('sha256', $value);
    }
}
