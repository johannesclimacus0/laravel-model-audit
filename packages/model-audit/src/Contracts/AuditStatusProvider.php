<?php

namespace Local\ModelAudit\Contracts;

use Local\ModelAudit\DTO\AuditStatus;

interface AuditStatusProvider
{
    public function get(): AuditStatus;
}
