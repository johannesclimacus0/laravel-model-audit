<?php

namespace Local\ModelAudit\Models;

use Illuminate\Database\Eloquent\Attributes\DateFormat;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Local\ModelAudit\Exceptions\AuditEntryIsImmutable;

#[Fillable([
    'subject_type',
    'subject_id',
    'actor_type',
    'actor_id',
    'event',
    'old_values',
    'new_values',
    'metadata',
    'ip_address',
    'user_agent',
    'request_id',
    'created_at',
])]
#[DateFormat('Y-m-d H:i:s.u')]
class AuditEntry extends Model
{
    use HasUuids;

    public const UPDATED_AT = null;

    protected static function booted(): void
    {
        static::updating(function (): void {
            throw AuditEntryIsImmutable::forUpdate();
        });

        static::deleting(function (): void {
            throw AuditEntryIsImmutable::forDelete();
        });
    }

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function getTable(): string
    {
        return (string) config('model-audit.table', 'audit_entries');
    }

    public function getConnectionName(): ?string
    {
        $connection = config('model-audit.connection');

        return is_string($connection) ? $connection : parent::getConnectionName();
    }

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'metadata' => 'array',
            'created_at' => 'immutable_datetime',
        ];
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function actor(): MorphTo
    {
        return $this->morphTo();
    }
}
