<?php

namespace Local\ModelAudit\Contracts;

use Local\ModelAudit\DTO\AuditChainIdentifier;

interface AuditChainFinder
{
    /**
     * @return iterable<AuditChainIdentifier>
     */
    public function all(): iterable;
}
