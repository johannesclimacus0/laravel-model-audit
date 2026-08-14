<?php

namespace Local\ModelAudit\Contracts;

use Local\ModelAudit\DTO\AuditEntryData;
use Local\ModelAudit\Models\AuditEntry;

interface AuditPayloadBuilder
{
    public function build(AuditEntryData $data, string $uuid, ?string $previousHash): array;

    public function buildFromEntry(AuditEntry $entry): array;
}
