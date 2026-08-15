<?php

namespace Local\ModelAudit\Tests\Feature\Console\Generators;

use Local\ModelAudit\Tests\Support\GeneratorTestCase;

class MakeAuditRequestIdResolverCommandTest extends GeneratorTestCase
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
}
