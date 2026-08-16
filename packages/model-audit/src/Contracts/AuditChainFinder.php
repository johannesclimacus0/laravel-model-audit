<?php

namespace Johannesclimacus\ModelAudit\Contracts;

use Johannesclimacus\ModelAudit\DTO\AuditChainIdentifier;

interface AuditChainFinder
{
    /**
     * @return iterable<AuditChainIdentifier>
     */
    public function all(): iterable;
}
