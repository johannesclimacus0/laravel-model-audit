<?php

namespace Johannesclimacus\ModelAudit\Recorders;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Johannesclimacus\ModelAudit\Contracts\AuditChainWriter;
use Johannesclimacus\ModelAudit\Contracts\AuditRecorder;
use Johannesclimacus\ModelAudit\DTO\AuditEntryData;
use Johannesclimacus\ModelAudit\Models\AuditEntry;

class DatabaseAuditRecorder implements AuditRecorder
{
    public function __construct(
        private AuditChainWriter $chainWriter,
    ) {}

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
