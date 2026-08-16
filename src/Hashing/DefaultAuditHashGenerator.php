<?php

namespace Johannesclimacus\ModelAudit\Hashing;

use Johannesclimacus\ModelAudit\Contracts\AuditCanonicalizer;
use Johannesclimacus\ModelAudit\Contracts\AuditHasher;
use Johannesclimacus\ModelAudit\Contracts\AuditHashGenerator;
use Johannesclimacus\ModelAudit\Contracts\AuditPayloadBuilder;
use Johannesclimacus\ModelAudit\DTO\AuditEntryData;
use Johannesclimacus\ModelAudit\Models\AuditEntry;

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
