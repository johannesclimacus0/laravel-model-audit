<?php

namespace Local\ModelAudit\Tests\Feature\Console\Generators;

use Local\ModelAudit\Tests\Support\GeneratorTestCase;

class MakeAuditUserAgentResolverCommandTest extends GeneratorTestCase
{
    public function test_it_creates_a_user_agent_resolver(): void
    {
        $this->assertGeneratorCreates(
            command: 'make:audit-user-agent-resolver',
            name: 'HeaderUserAgentResolver',
            relativePath: '/ModelAudit/Resolvers/HeaderUserAgentResolver.php',
            expectedContents: [
                'class HeaderUserAgentResolver implements UserAgentResolver',
                'public function resolve(): ?string',
                'return null;',
            ],
        );
    }
}
