<?php

namespace Local\ModelAudit\Recorders;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Local\ModelAudit\Contracts\AuditChainWriter;
use Local\ModelAudit\Contracts\AuditRecorder;
use Local\ModelAudit\DTO\AuditEntryData;
use Local\ModelAudit\Models\AuditEntry;

class DatabaseAuditRecorder implements AuditRecorder
{
    public function __construct(
        private AuditChainWriter $chainWriter,
    )
    {
    }

    public function record(AuditEntryData $data): ?AuditEntry
    {
        if (!config('model-audit.enabled', true)) {
            return null;
        }

        $this->ensureModelHasKey($data->subject, 'Subject');

        if ($data->actor !== null) {
            $this->ensureModelHasKey($data->actor, 'Actor');
        }

        return $this->chainWriter->append($data);
    }

    private function ensureModelHasKey(Model $model, string $role): void
    {
        if ($model->getKey() === null) {
            throw new InvalidArgumentException($role . ' must have a primary key.');
        }
    }
}
