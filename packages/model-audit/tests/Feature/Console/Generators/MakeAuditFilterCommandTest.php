<?php

namespace Local\ModelAudit\Tests\Feature\Console\Generators;

use Local\ModelAudit\Tests\Support\GeneratorTestCase;

class MakeAuditFilterCommandTest extends GeneratorTestCase
{
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
}
