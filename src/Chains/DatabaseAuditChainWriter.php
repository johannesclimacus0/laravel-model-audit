<?php

namespace Johannesclimacus\ModelAudit\Chains;

use Carbon\CarbonImmutable;
use Johannesclimacus\ModelAudit\Contracts\AuditChainWriter;
use Johannesclimacus\ModelAudit\Contracts\AuditHashGenerator;
use Johannesclimacus\ModelAudit\DTO\AuditEntryData;
use Johannesclimacus\ModelAudit\Models\AuditChainState;
use Johannesclimacus\ModelAudit\Models\AuditEntry;

class DatabaseAuditChainWriter implements AuditChainWriter
{
    public function __construct(
        private AuditHashGenerator $hashGenerator,
    ) {}

    public function append(AuditEntryData $data): AuditEntry
    {
        $subjectType = $data->subject->getMorphClass();
        $subjectId = (string) $data->subject->getKey();
        $connection = (new AuditChainState)->getConnection();

        return $connection->transaction(function () use ($data, $subjectType, $subjectId): AuditEntry {
            $state = $this->lockedState($subjectType, $subjectId);
            $entry = new AuditEntry;
            $uuid = $entry->newUniqueId();
            $previousHash = $state->last_hash;
            $hash = $this->hashGenerator->generate($data, $uuid, $previousHash);

            $entry->forceFill([
                'uuid' => $uuid,
                'subject_type' => $subjectType,
                'subject_id' => $subjectId,
                'actor_type' => $data->actor?->getMorphClass(),
                'actor_id' => $data->actor === null ? null : (string) $data->actor->getKey(),
                'event' => $data->event,
                'old_values' => $data->oldValues,
                'new_values' => $data->newValues,
                'metadata' => $data->metadata,
                'ip_address' => $data->ipAddress,
                'user_agent' => $data->userAgent,
                'request_id' => $data->requestId,
                'previous_hash' => $previousHash,
                'hash' => $hash,
                'created_at' => $data->createdAt,
            ])->save();

            $state->update([
                'last_hash' => $hash,
                'last_entry_uuid' => $uuid,
                'entries_count' => $state->entries_count + 1,
            ]);

            return $entry;
        });
    }

    private function lockedState(string $subjectType, string $subjectId): AuditChainState
    {
        $now = CarbonImmutable::now('UTC');

        AuditChainState::query()->insertOrIgnore([
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'entries_count' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return AuditChainState::query()
            ->where('subject_type', $subjectType)
            ->where('subject_id', $subjectId)
            ->lockForUpdate()
            ->sole();
    }
}
