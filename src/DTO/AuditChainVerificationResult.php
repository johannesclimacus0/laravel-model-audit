<?php

namespace Johannesclimacus\ModelAudit\DTO;

use Johannesclimacus\ModelAudit\Enums\AuditChainFailure;

class AuditChainVerificationResult
{
    public function __construct(
        public bool $valid,
        public ?AuditChainFailure $failure = null,
        public ?string $failedEntryUuid = null,
    ) {}

    public static function valid(): self
    {
        return new self(true);
    }

    public static function invalid(
        AuditChainFailure $failure,
        ?string $failedEntryUuid = null
    ): self {
        return new self(false, $failure, $failedEntryUuid);
    }
}
