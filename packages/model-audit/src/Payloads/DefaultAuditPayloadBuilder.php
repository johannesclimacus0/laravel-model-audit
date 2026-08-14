<?php

namespace Local\ModelAudit\Payloads;

use Local\ModelAudit\Contracts\AuditPayloadBuilder;
use Local\ModelAudit\DTO\AuditEntryData;
use Local\ModelAudit\Models\AuditEntry;

class DefaultAuditPayloadBuilder implements AuditPayloadBuilder
{
    public function build(AuditEntryData $data, string $uuid, ?string $previousHash): array {
        return [
            'uuid' => $uuid,
            'previous_hash' => $previousHash,
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
            'created_at' => $data->createdAt->format('Y-m-d\TH:i:s.u\Z'),
        ];
    }

    public function buildFromEntry(AuditEntry $entry): array {
        return [
            'uuid' => $entry->uuid,
            'previous_hash' => $entry->previous_hash,
            'subject_type' => $entry->subject_type,
            'subject_id' => $entry->subject_id,
            'actor_type' => $entry->actor_type,
            'actor_id' => $entry->actor_id,
            'event' => $entry->event,
            'old_values' => $entry->old_values,
            'new_values' => $entry->new_values,
            'metadata' => $entry->metadata,
            'ip_address' => $entry->ip_address,
            'user_agent' => $entry->user_agent,
            'request_id' => $entry->request_id,
            'created_at' => $entry->created_at->utc()->format('Y-m-d\TH:i:s.u\Z'),
        ];
    }
}
