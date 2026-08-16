<?php

namespace Johannesclimacus\ModelAudit\Contracts;

use Johannesclimacus\ModelAudit\DTO\AuditEntryData;
use Johannesclimacus\ModelAudit\Models\AuditEntry;

interface AuditChainWriter
{
    public function append(AuditEntryData $data): AuditEntry;
}
