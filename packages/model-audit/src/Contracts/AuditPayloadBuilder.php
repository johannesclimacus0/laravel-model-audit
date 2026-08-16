<?php

namespace Johannesclimacus\ModelAudit\Contracts;

use Johannesclimacus\ModelAudit\DTO\AuditEntryData;
use Johannesclimacus\ModelAudit\Models\AuditEntry;

interface AuditPayloadBuilder
{
    public function build(AuditEntryData $data, string $uuid, ?string $previousHash): array;

    public function buildFromEntry(AuditEntry $entry): array;
}
