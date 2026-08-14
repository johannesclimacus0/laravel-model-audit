<?php

namespace Local\ModelAudit\Contracts;

use Illuminate\Database\Eloquent\Model;
use Local\ModelAudit\Enums\ModelEvent;
use Local\ModelAudit\Models\AuditEntry;

interface AuditLogger
{
    public function record(
        Model $subject,
        string|ModelEvent $event,
        array $metadata = [],
        ?array $oldValues = null,
        ?array $newValues = null,
    ): ?AuditEntry;
}
