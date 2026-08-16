<?php

namespace Johannesclimacus\ModelAudit\Contracts;

use Johannesclimacus\ModelAudit\DTO\AuditEntryData;
use Johannesclimacus\ModelAudit\Models\AuditEntry;

interface AuditHashGenerator
{
    public function generate(AuditEntryData $data, string $uuid, ?string $previousHash): string;

    public function generateForEntry(AuditEntry $entry): string;
}
