<?php

namespace Local\ModelAudit\Contracts;

use Local\ModelAudit\DTO\AuditChainVerificationResult;

interface AuditChainVerifier
{
    public function verify(string $subjectType, string $subjectId): AuditChainVerificationResult;
}
