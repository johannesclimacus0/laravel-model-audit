<?php

namespace Local\ModelAudit\Exceptions;

use LogicException;

class AuditEntryIsImmutable extends LogicException
{
    public static function forUpdate(): self
    {
        return new self(__('model-audit::exceptions.immutable_update'));
    }

    public static function forDelete(): self
    {
        return new self(__('model-audit::exceptions.immutable_delete'));
    }
}
