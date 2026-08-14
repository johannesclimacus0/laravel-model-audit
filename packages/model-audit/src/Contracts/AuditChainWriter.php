<?php

namespace Local\ModelAudit\Contracts;

use Local\ModelAudit\DTO\AuditEntryData;
use Local\ModelAudit\Models\AuditEntry;

interface AuditChainWriter
{
    public function append(AuditEntryData $data): AuditEntry;
}
