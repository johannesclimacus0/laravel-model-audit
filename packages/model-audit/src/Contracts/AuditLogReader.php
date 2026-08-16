<?php

namespace Local\ModelAudit\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Local\ModelAudit\DTO\AuditLogQuery;
use Local\ModelAudit\Models\AuditEntry;

interface AuditLogReader
{
    /**
     * @return LengthAwarePaginator<int, AuditEntry>
     */
    public function paginate(AuditLogQuery $query): LengthAwarePaginator;
}
