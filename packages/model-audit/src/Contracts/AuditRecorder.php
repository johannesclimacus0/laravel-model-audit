<?php

namespace Johannesclimacus\ModelAudit\Contracts;

use Johannesclimacus\ModelAudit\DTO\AuditEntryData;
use Johannesclimacus\ModelAudit\Models\AuditEntry;

interface AuditRecorder
{
    public function record(AuditEntryData $data): ?AuditEntry;
}
