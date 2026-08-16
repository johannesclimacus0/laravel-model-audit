<?php

namespace Johannesclimacus\ModelAudit\Contracts;

use Illuminate\Database\Eloquent\Model;
use Johannesclimacus\ModelAudit\Enums\ModelEvent;
use Johannesclimacus\ModelAudit\Models\AuditEntry;

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
