<?php

namespace Local\ModelAudit;

use Illuminate\Support\ServiceProvider;
use Local\ModelAudit\Contracts\ActorResolver;
use Local\ModelAudit\Contracts\AuditAttributeFilter;
use Local\ModelAudit\Contracts\AuditRecorder;
use Local\ModelAudit\Contracts\AuditValueMasker;
use Local\ModelAudit\Filtering\DefaultAuditAttributeFilter;
use Local\ModelAudit\Masking\DefaultAuditValueMasker;
use Local\ModelAudit\Recorders\DatabaseAuditRecorder;
use Local\ModelAudit\Resolvers\AuthenticatedActorResolver;

class ModelAuditServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/model-audit.php', 'model-audit');

        $this->app->singleton(AuditRecorder::class, DatabaseAuditRecorder::class);

        $this->app->singleton(AuditAttributeFilter::class, DefaultAuditAttributeFilter::class);

        $this->app->singleton(AuditValueMasker::class, DefaultAuditValueMasker::class);

        $this->app->singleton(ActorResolver::class, AuthenticatedActorResolver::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'model-audit');

        $this->publishes([
            __DIR__ . '/../config/model-audit.php' => config_path('model-audit.php'),
        ], 'model-audit-config');

        $this->publishes([
            __DIR__ . '/../resources/lang' => lang_path('vendor/model-audit'),
        ], 'model-audit-lang');
    }
}
