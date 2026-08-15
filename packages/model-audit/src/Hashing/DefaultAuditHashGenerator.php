<?php

namespace Local\ModelAudit\Hashing;

use Local\ModelAudit\Contracts\AuditCanonicalizer;
use Local\ModelAudit\Contracts\AuditHasher;
use Local\ModelAudit\Contracts\AuditHashGenerator;
use Local\ModelAudit\Contracts\AuditPayloadBuilder;
use Local\ModelAudit\DTO\AuditEntryData;
use Local\ModelAudit\Models\AuditEntry;

class DefaultAuditHashGenerator implements AuditHashGenerator
{
    public function __construct(
        private AuditPayloadBuilder $payloadBuilder,
        private AuditCanonicalizer $canonicalizer,
        private AuditHasher $hasher,
    ) {}

    public function generate(AuditEntryData $data, string $uuid, ?string $previousHash): string
    {
        $payload = $this->payloadBuilder->build($data, $uuid, $previousHash);

        $canonicalPayload = $this->canonicalizer->canonicalize($payload);

        return $this->hasher->hash($canonicalPayload);
    }

    public function generateForEntry(AuditEntry $entry): string
    {
        $payload = $this->payloadBuilder->buildFromEntry($entry);

        $canonicalPayload = $this->canonicalizer->canonicalize($payload);

        return $this->hasher->hash($canonicalPayload);
    }
}
