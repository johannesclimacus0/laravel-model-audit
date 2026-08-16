<?php

namespace Johannesclimacus\ModelAudit\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Johannesclimacus\ModelAudit\DTO\AuditLogQuery;
use Johannesclimacus\ModelAudit\Models\AuditEntry;

interface AuditLogReader
{
    /**
     * @return LengthAwarePaginator<int, AuditEntry>
     */
    public function paginate(AuditLogQuery $query): LengthAwarePaginator;
}
