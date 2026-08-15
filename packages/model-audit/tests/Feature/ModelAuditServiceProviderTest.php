<?php

namespace Local\ModelAudit\Tests\Feature;

use Local\ModelAudit\Canonicalization\JsonAuditCanonicalizer;
use Local\ModelAudit\Chains\DatabaseAuditChainWriter;
use Local\ModelAudit\Contracts\ActorResolver;
use Local\ModelAudit\Contracts\AuditCanonicalizer;
use Local\ModelAudit\Contracts\AuditChainFinder;
use Local\ModelAudit\Contracts\AuditChainVerifier;
use Local\ModelAudit\Contracts\AuditChainWriter;
use Local\ModelAudit\Contracts\AuditHasher;
use Local\ModelAudit\Contracts\AuditHashGenerator;
use Local\ModelAudit\Contracts\AuditPayloadBuilder;
use Local\ModelAudit\Contracts\AuditStatusProvider;
use Local\ModelAudit\Contracts\IpAddressResolver;
use Local\ModelAudit\Contracts\RequestIdResolver;
use Local\ModelAudit\Contracts\UserAgentResolver;
use Local\ModelAudit\Hashing\DefaultAuditHashGenerator;
use Local\ModelAudit\Hashing\Sha256AuditHasher;
use Local\ModelAudit\Payloads\DefaultAuditPayloadBuilder;
use Local\ModelAudit\Resolvers\AuthenticatedActorResolver;
use Local\ModelAudit\Resolvers\RequestIpAddressResolver;
use Local\ModelAudit\Resolvers\RequestUserAgentResolver;
use Local\ModelAudit\Resolvers\UuidRequestIdResolver;
use Local\ModelAudit\Status\DatabaseAuditStatusProvider;
use Local\ModelAudit\Tests\TestCase;
use Local\ModelAudit\Verification\DatabaseAuditChainFinder;
use Local\ModelAudit\Verification\DatabaseAuditChainVerifier;

class ModelAuditServiceProviderTest extends TestCase
{
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
