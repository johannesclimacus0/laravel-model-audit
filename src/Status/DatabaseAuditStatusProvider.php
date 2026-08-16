<?php

namespace Johannesclimacus\ModelAudit\Status;

use Johannesclimacus\ModelAudit\Contracts\AuditStatusProvider;
use Johannesclimacus\ModelAudit\DTO\AuditStatus;
use Johannesclimacus\ModelAudit\Models\AuditChainState;
use Johannesclimacus\ModelAudit\Models\AuditEntry;

class DatabaseAuditStatusProvider implements AuditStatusProvider
{
    public function get(): AuditStatus
    {
        $entryModel = new AuditEntry;
        $chainStateModel = new AuditChainState;

        $lastEntry = AuditEntry::query()
            ->latest('id')
            ->first();

        return new AuditStatus(
            enabled: (bool) config('model-audit.enabled', true),
            connectionName: $entryModel->getConnection()->getName(),
            entriesTable: $entryModel->getTable(),
            chainStatesTable: $chainStateModel->getTable(),
            entriesCount: AuditEntry::query()->count(),
            chainsCount: AuditChainState::query()->count(),
            lastEntryAt: $lastEntry?->created_at,
        );
    }
}
