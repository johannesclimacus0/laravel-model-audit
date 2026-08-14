<?php

namespace Local\ModelAudit\Contracts;

use Illuminate\Database\Eloquent\Model;

interface AuditValueMasker
{
    public function mask(Model $model, array $values): array;
}
