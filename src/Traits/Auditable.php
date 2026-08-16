<?php

namespace Johannesclimacus\ModelAudit\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Johannesclimacus\ModelAudit\Models\AuditEntry;
use Johannesclimacus\ModelAudit\Observers\AuditableObserver;

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
        if (!property_exists($this, 'auditInclude')) {
            return [];
        }

        return $this->auditInclude;
    }

    public function auditExclude(): array
    {
        if (!property_exists($this, 'auditExclude')) {
            return [];
        }

        return $this->auditExclude;
    }

    public function auditMasks(): array
    {
        if (!property_exists($this, 'auditMasks')) {
            return [];
        }

        return $this->auditMasks;
    }
}
