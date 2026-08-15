<?php

namespace Local\ModelAudit\Tests\Feature;

use Local\ModelAudit\Tests\GeneratorTestCase;

class MakeAuditExtensionCommandsTest extends GeneratorTestCase
{
    public function test_it_creates_a_request_id_resolver(): void
    {
        $this->assertGeneratorCreates(
            command: 'make:audit-request-id-resolver',
            name: 'HeaderRequestIdResolver',
            relativePath: '/ModelAudit/Resolvers/HeaderRequestIdResolver.php',
            expectedContents: [
                'class HeaderRequestIdResolver implements RequestIdResolver',
                'public function resolve(): ?string',
                'return null;',
            ],
        );
    }

    public function test_it_creates_an_ip_address_resolver(): void
    {
        $this->assertGeneratorCreates(
            command: 'make:audit-ip-resolver',
            name: 'TrustedProxyIpResolver',
            relativePath: '/ModelAudit/Resolvers/TrustedProxyIpResolver.php',
            expectedContents: [
                'class TrustedProxyIpResolver implements IpAddressResolver',
                'public function resolve(): ?string',
                'return null;',
            ],
        );
    }

    public function test_it_creates_an_audit_hasher(): void
    {
        $this->assertGeneratorCreates(
            command: 'make:audit-hasher',
            name: 'CustomAuditHasher',
            relativePath: '/ModelAudit/Hashing/CustomAuditHasher.php',
            expectedContents: [
                'class CustomAuditHasher implements AuditHasher',
                'public function hash(string $value): string',
                "return hash('sha256', \$value);",
            ],
        );
    }

    public function test_it_creates_an_audit_attribute_filter(): void
    {
        $this->assertGeneratorCreates(
            command: 'make:audit-filter',
            name: 'CustomAuditFilter',
            relativePath: '/ModelAudit/Filtering/CustomAuditFilter.php',
            expectedContents: [
                'class CustomAuditFilter implements AuditAttributeFilter',
                'public function filter(Model $model, array $values): array',
                'return $values;',
            ],
        );
    }

    public function test_it_creates_a_user_agent_resolver(): void
    {
        $this->assertGeneratorCreates(
            command: 'make:audit-user-agent-resolver',
            name: 'UserAgentResolver',
            relativePath: '/ModelAudit/Resolvers/UserAgentResolver.php',
            expectedContents: [
                'class UserAgentResolver implements UserAgentResolver',
                'public function resolve(): ?string',
                'return null;',
            ],
        );
    }
}
