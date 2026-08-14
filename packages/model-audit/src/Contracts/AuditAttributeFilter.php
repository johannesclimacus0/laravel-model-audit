<?php

namespace Local\ModelAudit\Contracts;

use Illuminate\Database\Eloquent\Model;

interface AuditAttributeFilter
{
    public function filter(Model $model, array $values): array;
}
