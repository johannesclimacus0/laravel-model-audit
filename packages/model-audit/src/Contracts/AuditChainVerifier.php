<?php

namespace Johannesclimacus\ModelAudit\Contracts;

use Johannesclimacus\ModelAudit\DTO\AuditChainVerificationResult;

interface AuditChainVerifier
{
    public function verify(string $subjectType, string $subjectId): AuditChainVerificationResult;
}
