<?php

namespace Johannesclimacus\ModelAudit\History;

use Illuminate\Database\Eloquent\Collection;
use Johannesclimacus\ModelAudit\Contracts\AuditHistoryReader;
use Johannesclimacus\ModelAudit\DTO\AuditHistoryQuery;
use Johannesclimacus\ModelAudit\Models\AuditEntry;

class DatabaseAuditHistoryReader implements AuditHistoryReader
{
    public function read(AuditHistoryQuery $query): Collection
    {
        $entries = AuditEntry::query()
            ->where('subject_type', $query->subjectType)
            ->where('subject_id', $query->subjectId);

        if ($query->event !== null) {
            $entries->where('event', $query->event);
        }

        return $entries
            ->orderByDesc('id')
            ->limit($query->limit)
            ->get();
    }
}
