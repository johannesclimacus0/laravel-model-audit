<?php

namespace Local\ModelAudit\Verification;

use Local\ModelAudit\Contracts\AuditChainVerifier;
use Local\ModelAudit\Contracts\AuditHashGenerator;
use Local\ModelAudit\DTO\AuditChainVerificationResult;
use Local\ModelAudit\Enums\AuditChainFailure;
use Local\ModelAudit\Models\AuditChainState;
use Local\ModelAudit\Models\AuditEntry;

class DatabaseAuditChainVerifier implements AuditChainVerifier
{
    public function __construct(
        private AuditHashGenerator $hashGenerator,
    ) {}

    public function verify(string $subjectType, string $subjectId): AuditChainVerificationResult
    {
        $entries = AuditEntry::query()
            ->where('subject_type', $subjectType)
            ->where('subject_id', $subjectId)
            ->orderBy('id')
            ->get();

        $state = AuditChainState::query()
            ->where('subject_type', $subjectType)
            ->where('subject_id', $subjectId)
            ->first();

        if ($state === null && $entries->isEmpty()) {
            return AuditChainVerificationResult::valid();
        }

        if ($state === null) {
            return AuditChainVerificationResult::invalid(
                AuditChainFailure::StateMissing,
                $entries->first()?->uuid,
            );
        }

        $previousHash = null;

        foreach ($entries as $entry) {
            if (!$this->hashesMatch($previousHash, $entry->previous_hash)) {
                return AuditChainVerificationResult::invalid(
                    AuditChainFailure::PreviousHashMismatch,
                    $entry->uuid,
                );
            }

            $calculatedHash = $this->hashGenerator->generateForEntry($entry);

            if (!$this->hashesMatch($calculatedHash, $entry->hash)) {
                return AuditChainVerificationResult::invalid(
                    AuditChainFailure::HashMismatch,
                    $entry->uuid,
                );
            }

            $previousHash = $entry->hash;
        }

        if ($state->entries_count !== $entries->count()) {
            return AuditChainVerificationResult::invalid(
                AuditChainFailure::EntryCountMismatch,
            );
        }

        if (!$this->hashesMatch($previousHash, $state->last_hash)) {
            return AuditChainVerificationResult::invalid(
                AuditChainFailure::LastHashMismatch,
                $entries->last()?->uuid,
            );
        }

        if ($state->last_entry_uuid !== $entries->last()?->uuid) {
            return AuditChainVerificationResult::invalid(
                AuditChainFailure::LastEntryUuidMismatch,
                $entries->last()?->uuid,
            );
        }

        return AuditChainVerificationResult::valid();
    }

    private function hashesMatch(?string $expected, ?string $actual): bool
    {
        if ($expected === null || $actual === null) {
            return $expected === $actual;
        }

        return hash_equals($expected, $actual);
    }
}
