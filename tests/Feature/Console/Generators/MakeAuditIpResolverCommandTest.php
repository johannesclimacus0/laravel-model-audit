<?php

namespace Johannesclimacus\ModelAudit\Tests\Feature\Console\Generators;

use Johannesclimacus\ModelAudit\Tests\Support\GeneratorTestCase;

class MakeAuditIpResolverCommandTest extends GeneratorTestCase
{
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
}
