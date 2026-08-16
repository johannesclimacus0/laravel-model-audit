<?php

namespace Johannesclimacus\ModelAudit;

use Illuminate\Support\ServiceProvider;
use Johannesclimacus\ModelAudit\Canonicalization\JsonAuditCanonicalizer;
use Johannesclimacus\ModelAudit\Chains\DatabaseAuditChainWriter;
use Johannesclimacus\ModelAudit\Console\Commands\AuditStatusCommand;
use Johannesclimacus\ModelAudit\Console\Commands\ShowAuditHistoryCommand;
use Johannesclimacus\ModelAudit\Console\Commands\VerifyAllAuditChainsCommand;
use Johannesclimacus\ModelAudit\Console\Commands\VerifyAuditChainCommand;
use Johannesclimacus\ModelAudit\Console\Generators\MakeAuditableModelCommand;
use Johannesclimacus\ModelAudit\Console\Generators\MakeAuditActorResolverCommand;
use Johannesclimacus\ModelAudit\Console\Generators\MakeAuditFilterCommand;
use Johannesclimacus\ModelAudit\Console\Generators\MakeAuditHasherCommand;
use Johannesclimacus\ModelAudit\Console\Generators\MakeAuditIpResolverCommand;
use Johannesclimacus\ModelAudit\Console\Generators\MakeAuditRequestIdResolverCommand;
use Johannesclimacus\ModelAudit\Console\Generators\MakeAuditUserAgentResolverCommand;
use Johannesclimacus\ModelAudit\Contracts\ActorResolver;
use Johannesclimacus\ModelAudit\Contracts\AuditAttributeFilter;
use Johannesclimacus\ModelAudit\Contracts\AuditCanonicalizer;
use Johannesclimacus\ModelAudit\Contracts\AuditChainFinder;
use Johannesclimacus\ModelAudit\Contracts\AuditChainVerifier;
use Johannesclimacus\ModelAudit\Contracts\AuditChainWriter;
use Johannesclimacus\ModelAudit\Contracts\AuditHasher;
use Johannesclimacus\ModelAudit\Contracts\AuditHashGenerator;
use Johannesclimacus\ModelAudit\Contracts\AuditHistoryReader;
use Johannesclimacus\ModelAudit\Contracts\AuditLogger;
use Johannesclimacus\ModelAudit\Contracts\AuditLogReader;
use Johannesclimacus\ModelAudit\Contracts\AuditPayloadBuilder;
use Johannesclimacus\ModelAudit\Contracts\AuditRecorder;
use Johannesclimacus\ModelAudit\Contracts\AuditStatusProvider;
use Johannesclimacus\ModelAudit\Contracts\AuditValueMasker;
use Johannesclimacus\ModelAudit\Contracts\IpAddressResolver;
use Johannesclimacus\ModelAudit\Contracts\RequestIdResolver;
use Johannesclimacus\ModelAudit\Contracts\UserAgentResolver;
use Johannesclimacus\ModelAudit\Filtering\DefaultAuditAttributeFilter;
use Johannesclimacus\ModelAudit\Hashing\DefaultAuditHashGenerator;
use Johannesclimacus\ModelAudit\Hashing\Sha256AuditHasher;
use Johannesclimacus\ModelAudit\History\DatabaseAuditHistoryReader;
use Johannesclimacus\ModelAudit\History\DatabaseAuditLogReader;
use Johannesclimacus\ModelAudit\Logging\DefaultAuditLogger;
use Johannesclimacus\ModelAudit\Masking\DefaultAuditValueMasker;
use Johannesclimacus\ModelAudit\Payloads\DefaultAuditPayloadBuilder;
use Johannesclimacus\ModelAudit\Recorders\DatabaseAuditRecorder;
use Johannesclimacus\ModelAudit\Resolvers\AuthenticatedActorResolver;
use Johannesclimacus\ModelAudit\Resolvers\RequestIpAddressResolver;
use Johannesclimacus\ModelAudit\Resolvers\RequestUserAgentResolver;
use Johannesclimacus\ModelAudit\Resolvers\UuidRequestIdResolver;
use Johannesclimacus\ModelAudit\Status\DatabaseAuditStatusProvider;
use Johannesclimacus\ModelAudit\Verification\DatabaseAuditChainFinder;
use Johannesclimacus\ModelAudit\Verification\DatabaseAuditChainVerifier;

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

        $this->app->singleton(AuditChainFinder::class, DatabaseAuditChainFinder::class);

        $this->app->singleton(AuditStatusProvider::class, DatabaseAuditStatusProvider::class);

        $this->app->singleton(AuditHistoryReader::class, DatabaseAuditHistoryReader::class);

        $this->app->singleton(AuditLogReader::class, DatabaseAuditLogReader::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'model-audit');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'model-audit');

        if (config('model-audit.ui.enabled', true)) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        }

        if ($this->app->runningInConsole()) {
            $this->commands([
                AuditStatusCommand::class,
                ShowAuditHistoryCommand::class,
                VerifyAllAuditChainsCommand::class,
                VerifyAuditChainCommand::class,
                MakeAuditableModelCommand::class,
                MakeAuditActorResolverCommand::class,
                MakeAuditRequestIdResolverCommand::class,
                MakeAuditIpResolverCommand::class,
                MakeAuditHasherCommand::class,
                MakeAuditFilterCommand::class,
                MakeAuditUserAgentResolverCommand::class,
            ]);
        }

        $this->publishes([
            __DIR__ . '/../config/model-audit.php' => config_path('model-audit.php'),
        ], 'model-audit-config');

        $this->publishes([
            __DIR__ . '/../resources/lang' => lang_path('vendor/model-audit'),
        ], 'model-audit-lang');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/model-audit'),
        ], 'model-audit-views');
    }
}
