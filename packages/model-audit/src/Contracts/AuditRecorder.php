<?php

namespace Local\ModelAudit\Contracts;

use Local\ModelAudit\DTO\AuditEntryData;
use Local\ModelAudit\Models\AuditEntry;

interface AuditRecorder
{
    public function record(AuditEntryData $data): ?AuditEntry;
}
