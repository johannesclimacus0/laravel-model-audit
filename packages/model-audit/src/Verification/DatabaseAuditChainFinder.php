<?php

namespace Local\ModelAudit\Verification;

use Local\ModelAudit\Contracts\AuditChainFinder;
use Local\ModelAudit\DTO\AuditChainIdentifier;
use Local\ModelAudit\Models\AuditChainState;
use Local\ModelAudit\Models\AuditEntry;

class DatabaseAuditChainFinder implements AuditChainFinder
{
    public function all(): iterable
    {
        $entrySubjects = AuditEntry::query()
            ->select([
                'subject_type',
                'subject_id',
            ])
            ->toBase();

        $stateSubjects = AuditChainState::query()
            ->select([
                'subject_type',
                'subject_id',
            ])
            ->toBase();

        $rows = $entrySubjects
            ->union($stateSubjects)
            ->orderBy('subject_type')
            ->orderBy('subject_id')
            ->cursor();

        foreach ($rows as $row) {
            yield new AuditChainIdentifier(
                subjectType: (string) $row->subject_type,
                subjectId: (string) $row->subject_id,
            );
        }
    }
}
