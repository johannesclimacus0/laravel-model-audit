<?php

namespace Local\ModelAudit\Contracts;

interface AuditHasher
{
    public function hash(string $value): string;
}
