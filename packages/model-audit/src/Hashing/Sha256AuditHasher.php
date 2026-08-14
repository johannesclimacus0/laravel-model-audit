<?php

namespace Local\ModelAudit\Hashing;

use Local\ModelAudit\Contracts\AuditHasher;

class Sha256AuditHasher implements AuditHasher
{

    public function hash(string $value): string
    {
        return hash('sha256', $value);
    }
}
