<?php

namespace Local\ModelAudit\Filtering;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Local\ModelAudit\Contracts\AuditAttributeFilter;

class DefaultAuditAttributeFilter implements AuditAttributeFilter
{
    public function filter(Model $model, array $values): array
    {
        $include = $model->auditInclude();

        if ($include !== []) {
            return Arr::only($values, $include);
        }

        $exclude = [
            $model->getKeyName(),
            $model->getCreatedAtColumn(),
            $model->getUpdatedAtColumn(),
        ];


        if(method_exists($model, 'getDeletedAtColumn')) {
            $exclude[] = $model->getDeletedAtColumn();
        }

        $exclude = array_merge($exclude, $model->auditExclude());

        $exclude = array_filter($exclude, fn ($field) => is_string($field) && $field !== '');

        return Arr::except($values, $exclude);
    }
}
