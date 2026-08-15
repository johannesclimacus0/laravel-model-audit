<?php

namespace Local\ModelAudit\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Local\ModelAudit\DTO\AuditHistoryQuery;
use Local\ModelAudit\Models\AuditEntry;

interface AuditHistoryReader
{
    /**
     * @return Collection<int, AuditEntry>
     */
    public function read(AuditHistoryQuery $query): Collection;
}
