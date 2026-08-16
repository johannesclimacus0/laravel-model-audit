<?php

namespace Johannesclimacus\ModelAudit\History;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Johannesclimacus\ModelAudit\Contracts\AuditLogReader;
use Johannesclimacus\ModelAudit\DTO\AuditLogQuery;
use Johannesclimacus\ModelAudit\Models\AuditEntry;

class DatabaseAuditLogReader implements AuditLogReader
{
    public function paginate(AuditLogQuery $query): LengthAwarePaginator
    {
        return AuditEntry::query()
            ->when(
                $query->event,
                fn ($entries, string $event) => $entries->where('event', $event),
            )
            ->when(
                $query->subjectType,
                fn ($entries, string $type) => $entries->where('subject_type', $type),
            )
            ->when(
                $query->subjectId,
                fn ($entries, string $id) => $entries->where('subject_id', $id),
            )
            ->when(
                $query->actorType,
                fn ($entries, string $type) => $entries->where('actor_type', $type),
            )
            ->when(
                $query->actorId,
                fn ($entries, string $id) => $entries->where('actor_id', $id),
            )
            ->when(
                $query->requestId,
                fn ($entries, string $id) => $entries->where('request_id', $id),
            )
            ->when(
                $query->dateFrom,
                fn ($entries, $date) => $entries->where('created_at', '>=', $date),
            )
            ->when(
                $query->dateTo,
                fn ($entries, $date) => $entries->where('created_at', '<=', $date),
            )
            ->orderByDesc('id')
            ->paginate($query->perPage);
    }
}
