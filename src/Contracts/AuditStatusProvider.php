<?php

namespace Johannesclimacus\ModelAudit\Contracts;

use Johannesclimacus\ModelAudit\DTO\AuditStatus;

interface AuditStatusProvider
{
    public function get(): AuditStatus;
}
