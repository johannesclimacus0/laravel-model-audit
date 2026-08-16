<?php

namespace Johannesclimacus\ModelAudit\Tests\Feature;

use Johannesclimacus\ModelAudit\Canonicalization\JsonAuditCanonicalizer;
use Johannesclimacus\ModelAudit\Chains\DatabaseAuditChainWriter;
use Johannesclimacus\ModelAudit\Contracts\ActorResolver;
use Johannesclimacus\ModelAudit\Contracts\AuditCanonicalizer;
use Johannesclimacus\ModelAudit\Contracts\AuditChainFinder;
use Johannesclimacus\ModelAudit\Contracts\AuditChainVerifier;
use Johannesclimacus\ModelAudit\Contracts\AuditChainWriter;
use Johannesclimacus\ModelAudit\Contracts\AuditHasher;
use Johannesclimacus\ModelAudit\Contracts\AuditHashGenerator;
use Johannesclimacus\ModelAudit\Contracts\AuditHistoryReader;
use Johannesclimacus\ModelAudit\Contracts\AuditLogReader;
use Johannesclimacus\ModelAudit\Contracts\AuditPayloadBuilder;
use Johannesclimacus\ModelAudit\Contracts\AuditStatusProvider;
use Johannesclimacus\ModelAudit\Contracts\IpAddressResolver;
use Johannesclimacus\ModelAudit\Contracts\RequestIdResolver;
use Johannesclimacus\ModelAudit\Contracts\UserAgentResolver;
use Johannesclimacus\ModelAudit\Hashing\DefaultAuditHashGenerator;
use Johannesclimacus\ModelAudit\Hashing\Sha256AuditHasher;
use Johannesclimacus\ModelAudit\History\DatabaseAuditHistoryReader;
use Johannesclimacus\ModelAudit\History\DatabaseAuditLogReader;
use Johannesclimacus\ModelAudit\Payloads\DefaultAuditPayloadBuilder;
use Johannesclimacus\ModelAudit\Resolvers\AuthenticatedActorResolver;
use Johannesclimacus\ModelAudit\Resolvers\RequestIpAddressResolver;
use Johannesclimacus\ModelAudit\Resolvers\RequestUserAgentResolver;
use Johannesclimacus\ModelAudit\Resolvers\UuidRequestIdResolver;
use Johannesclimacus\ModelAudit\Status\DatabaseAuditStatusProvider;
use Johannesclimacus\ModelAudit\Tests\TestCase;
use Johannesclimacus\ModelAudit\Verification\DatabaseAuditChainFinder;
use Johannesclimacus\ModelAudit\Verification\DatabaseAuditChainVerifier;

class ModelAuditServiceProviderTest extends TestCase
{
    public function test_it_registers_the_database_audit_log_reader(): void
    {
        $reader = $this->app->make(AuditLogReader::class);

        $this->assertInstanceOf(DatabaseAuditLogReader::class, $reader);
        $this->assertSame($reader, $this->app->make(AuditLogReader::class));
    }

    public function test_it_registers_the_database_audit_history_reader(): void
    {
        $reader = $this->app->make(AuditHistoryReader::class);

        $this->assertInstanceOf(DatabaseAuditHistoryReader::class, $reader);
        $this->assertSame($reader, $this->app->make(AuditHistoryReader::class));
    }

    public function test_it_registers_the_database_audit_chain_finder(): void
    {
        $finder = $this->app->make(AuditChainFinder::class);

        $this->assertInstanceOf(DatabaseAuditChainFinder::class, $finder);
        $this->assertSame($finder, $this->app->make(AuditChainFinder::class));
    }

    public function test_it_registers_the_database_audit_chain_writer(): void
    {
        $writer = $this->app->make(AuditChainWriter::class);

        $this->assertInstanceOf(DatabaseAuditChainWriter::class, $writer);
        $this->assertSame($writer, $this->app->make(AuditChainWriter::class));
    }

    public function test_it_registers_the_database_audit_chain_verifier(): void
    {
        $verifier = $this->app->make(AuditChainVerifier::class);

        $this->assertInstanceOf(DatabaseAuditChainVerifier::class, $verifier);
        $this->assertSame($verifier, $this->app->make(AuditChainVerifier::class));
    }

    public function test_it_registers_the_default_audit_canonicalizer(): void
    {
        $canonicalizer = $this->app->make(AuditCanonicalizer::class);

        $this->assertInstanceOf(JsonAuditCanonicalizer::class, $canonicalizer);
        $this->assertSame($canonicalizer, $this->app->make(AuditCanonicalizer::class));
    }

    public function test_it_registers_the_default_audit_hasher(): void
    {
        $hasher = $this->app->make(AuditHasher::class);

        $this->assertInstanceOf(Sha256AuditHasher::class, $hasher);
        $this->assertSame($hasher, $this->app->make(AuditHasher::class));
    }

    public function test_it_registers_the_default_audit_hash_generator(): void
    {
        $generator = $this->app->make(AuditHashGenerator::class);

        $this->assertInstanceOf(DefaultAuditHashGenerator::class, $generator);
        $this->assertSame($generator, $this->app->make(AuditHashGenerator::class));
    }

    public function test_it_registers_the_default_audit_payload_builder(): void
    {
        $builder = $this->app->make(AuditPayloadBuilder::class);

        $this->assertInstanceOf(DefaultAuditPayloadBuilder::class, $builder);
        $this->assertSame($builder, $this->app->make(AuditPayloadBuilder::class));
    }

    public function test_it_registers_the_default_actor_resolver(): void
    {
        $resolver = $this->app->make(ActorResolver::class);

        $this->assertInstanceOf(AuthenticatedActorResolver::class, $resolver);

        $this->assertSame($resolver, $this->app->make(ActorResolver::class));
    }

    public function test_it_registers_the_default_request_context_resolvers(): void
    {
        $resolver = $this->app->make(IpAddressResolver::class);
        $this->assertInstanceOf(RequestIpAddressResolver::class, $resolver);
        $this->assertSame($resolver, $this->app->make(IpAddressResolver::class));

        $resolver = $this->app->make(UserAgentResolver::class);
        $this->assertInstanceOf(RequestUserAgentResolver::class, $resolver);
        $this->assertSame($resolver, $this->app->make(UserAgentResolver::class));

        $resolver = $this->app->make(RequestIdResolver::class);
        $this->assertInstanceOf(UuidRequestIdResolver::class, $resolver);
        $this->assertSame($resolver, $this->app->make(RequestIdResolver::class));
    }

    public function test_it_registers_the_database_audit_status_provider(): void
    {
        $provider = $this->app->make(AuditStatusProvider::class);

        $this->assertInstanceOf(DatabaseAuditStatusProvider::class, $provider);
        $this->assertSame($provider, $this->app->make(AuditStatusProvider::class));
    }
}
