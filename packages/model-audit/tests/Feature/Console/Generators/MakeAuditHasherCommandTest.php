<?php

namespace Local\ModelAudit\Tests\Feature\Console\Generators;

use Local\ModelAudit\Tests\Support\GeneratorTestCase;

class MakeAuditHasherCommandTest extends GeneratorTestCase
{
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
}
