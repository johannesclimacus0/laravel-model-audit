<?php

namespace Local\ModelAudit;

use Illuminate\Support\ServiceProvider;
use Local\ModelAudit\Canonicalization\JsonAuditCanonicalizer;
use Local\ModelAudit\Chains\DatabaseAuditChainWriter;
use Local\ModelAudit\Console\MakeAuditableModelCommand;
use Local\ModelAudit\Console\VerifyAuditChainCommand;
use Local\ModelAudit\Contracts\ActorResolver;
use Local\ModelAudit\Contracts\AuditAttributeFilter;
use Local\ModelAudit\Contracts\AuditCanonicalizer;
use Local\ModelAudit\Contracts\AuditChainVerifier;
use Local\ModelAudit\Contracts\AuditChainWriter;
use Local\ModelAudit\Contracts\AuditHasher;
use Local\ModelAudit\Contracts\AuditHashGenerator;
use Local\ModelAudit\Contracts\AuditLogger;
use Local\ModelAudit\Contracts\AuditPayloadBuilder;
use Local\ModelAudit\Contracts\AuditRecorder;
use Local\ModelAudit\Contracts\AuditValueMasker;
use Local\ModelAudit\Contracts\IpAddressResolver;
use Local\ModelAudit\Contracts\RequestIdResolver;
use Local\ModelAudit\Contracts\UserAgentResolver;
use Local\ModelAudit\Filtering\DefaultAuditAttributeFilter;
use Local\ModelAudit\Hashing\DefaultAuditHashGenerator;
use Local\ModelAudit\Hashing\Sha256AuditHasher;
use Local\ModelAudit\Logging\DefaultAuditLogger;
use Local\ModelAudit\Masking\DefaultAuditValueMasker;
use Local\ModelAudit\Payloads\DefaultAuditPayloadBuilder;
use Local\ModelAudit\Recorders\DatabaseAuditRecorder;
use Local\ModelAudit\Resolvers\AuthenticatedActorResolver;
use Local\ModelAudit\Resolvers\RequestIpAddressResolver;
use Local\ModelAudit\Resolvers\RequestUserAgentResolver;
use Local\ModelAudit\Resolvers\UuidRequestIdResolver;
use Local\ModelAudit\Verification\DatabaseAuditChainVerifier;

class ModelAuditServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/model-audit.php', 'model-audit');

        $this->app->singleton(AuditRecorder::class, DatabaseAuditRecorder::class);

        $this->app->singleton(AuditAttributeFilter::class, DefaultAuditAttributeFilter::class);

        $this->app->singleton(AuditValueMasker::class, DefaultAuditValueMasker::class);

        $this->app->singleton(AuditCanonicalizer::class, JsonAuditCanonicalizer::class);

        $this->app->singleton(AuditPayloadBuilder::class, DefaultAuditPayloadBuilder::class);

        $this->app->singleton(AuditHashGenerator::class, DefaultAuditHashGenerator::class);

        $this->app->singleton(AuditChainWriter::class, DatabaseAuditChainWriter::class);

        $this->app->singleton(AuditChainVerifier::class, DatabaseAuditChainVerifier::class);

        $this->app->singleton(ActorResolver::class, AuthenticatedActorResolver::class);

        $this->app->scoped(IpAddressResolver::class, RequestIpAddressResolver::class);

        $this->app->scoped(UserAgentResolver::class, RequestUserAgentResolver::class);

        $this->app->scoped(RequestIdResolver::class, UuidRequestIdResolver::class);

        $this->app->scoped(AuditLogger::class, DefaultAuditLogger::class);

        $this->app->singleton(AuditHasher::class, Sha256AuditHasher::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'model-audit');

        if ($this->app->runningInConsole()) {
            $this->commands([
                VerifyAuditChainCommand::class,
                MakeAuditableModelCommand::class,
            ]);
        }

        $this->publishes([
            __DIR__ . '/../config/model-audit.php' => config_path('model-audit.php'),
        ], 'model-audit-config');

        $this->publishes([
            __DIR__ . '/../resources/lang' => lang_path('vendor/model-audit'),
        ], 'model-audit-lang');
    }
}
