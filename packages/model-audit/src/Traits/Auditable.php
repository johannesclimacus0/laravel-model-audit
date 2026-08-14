<?php

namespace Local\ModelAudit\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Local\ModelAudit\Models\AuditEntry;
use Local\ModelAudit\Observers\AuditableObserver;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($model): void {
            app(AuditableObserver::class)->created($model);
        });

        static::updated(function ($model): void {
            app(AuditableObserver::class)->updated($model);
        });

        static::deleted(function ($model): void {
            app(AuditableObserver::class)->deleted($model);
        });

        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model): void {
                app(AuditableObserver::class)->restored($model);
            });
        }
    }

    public function audits(): MorphMany
    {
        return $this->morphMany(AuditEntry::class, 'subject');
    }

    public function auditInclude(): array
    {
        return [];
    }

    public function auditExclude(): array
    {
        return [];
    }

    public function auditMasks(): array
    {
        return [];
    }
}
