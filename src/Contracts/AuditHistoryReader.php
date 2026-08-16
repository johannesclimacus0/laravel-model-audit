<?php

namespace Johannesclimacus\ModelAudit\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Johannesclimacus\ModelAudit\DTO\AuditHistoryQuery;
use Johannesclimacus\ModelAudit\Models\AuditEntry;

interface AuditHistoryReader
{
    /**
     * @return Collection<int, AuditEntry>
     */
    public function read(AuditHistoryQuery $query): Collection;
}
