<?php

namespace Local\ModelAudit\Tests\Feature;

use Local\ModelAudit\Tests\GeneratorTestCase;

class MakeAuditableModelCommandTest extends GeneratorTestCase
{
    public function test_it_makes_auditable_model(): void
    {
        $this->assertGeneratorCreates(
            command: 'make:auditable-model',
            name: 'Invoice',
            relativePath: '/Models/Invoice.php',
            expectedContents: [
                'class Invoice extends Model',
                'use Auditable;',
                'protected array $auditInclude = [];',
            ],
        );
    }

    public function test_it_creates_a_model_in_a_nested_namespace(): void
    {
        $this->assertGeneratorCreates(
            command: 'make:auditable-model',
            name: 'Billing/Invoice',
            relativePath: '/Models/Billing/Invoice.php',
            expectedContents: [
                'class Invoice extends Model',
            ],
        );
    }

    public function test_it_does_not_overwrite_an_existing_model(): void
    {
        $path = $this->tmpAppPath . '/Models/Invoice.php';

        $this->filesystem->makeDirectory(dirname($path), 0777, true);

        $this->filesystem->put($path, 'existing model');

        $this->artisan('make:auditable-model', [
            'name' => 'Invoice',
        ])->assertFailed();

        $this->assertSame(
            'existing model',
            $this->filesystem->get($path),
        );
    }

    public function test_it_overwrites_an_existing_model_with_force(): void
    {
        $path = $this->tmpAppPath . '/Models/Invoice.php';

        $this->filesystem->makeDirectory(dirname($path), 0777, true);

        $this->filesystem->put($path, 'existing model');

        $this->artisan('make:auditable-model', [
            'name' => 'Invoice',
            '--force' => true,
        ])->assertSuccessful();

        $contents = $this->filesystem->get($path);

        $this->assertStringNotContainsString(
            'existing model',
            $contents,
        );

        $this->assertStringContainsString(
            'class Invoice extends Model',
            $contents,
        );

        $this->assertStringContainsString(
            'use Auditable;',
            $contents,
        );
    }
}
