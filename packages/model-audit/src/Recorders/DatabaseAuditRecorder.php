<?php

namespace Local\ModelAudit\Recorders;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Local\ModelAudit\Contracts\AuditRecorder;
use Local\ModelAudit\DTO\AuditEntryData;
use Local\ModelAudit\Models\AuditEntry;

class DatabaseAuditRecorder implements AuditRecorder
{
    public function record(AuditEntryData $data): ?AuditEntry
    {
        if (!config('model-audit.enabled', true)) {
            return null;
        }

        $this->ensureModelHasKey(
            $data->subject,
            'Subject',
        );

        if ($data->actor !== null) {
            $this->ensureModelHasKey(
                $data->actor,
                'Actor',
            );
        }

        return AuditEntry::query()->create([
            'subject_type' => $data->subject->getMorphClass(),
            'subject_id' => (string) $data->subject->getKey(),
            'actor_type' => $data->actor?->getMorphClass(),
            'actor_id' => $data->actor === null ? null : (string) $data->actor->getKey(),
            'event' => $data->event,
            'old_values' => $data->oldValues,
            'new_values' => $data->newValues,
            'metadata' => $data->metadata,
            'ip_address' => $data->ipAddress,
            'user_agent' => $data->userAgent,
            'request_id' => $data->requestId,
            'created_at' => $data->createdAt,
        ]);
    }

    private function ensureModelHasKey(Model $model, string $role): void
    {
        if ($model->getKey() === null) {
            throw new InvalidArgumentException($role . ' must have a primary key.');
        }
    }
}
