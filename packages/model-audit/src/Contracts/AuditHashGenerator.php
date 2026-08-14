<?php

namespace Local\ModelAudit\Contracts;

use Local\ModelAudit\DTO\AuditEntryData;
use Local\ModelAudit\Models\AuditEntry;

interface AuditHashGenerator
{
    public function generate(AuditEntryData $data, string $uuid, ?string $previousHash): string;

    public function generateForEntry(AuditEntry $entry): string;
}
