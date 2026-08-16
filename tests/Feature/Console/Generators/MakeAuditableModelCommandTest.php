<?php

namespace Johannesclimacus\ModelAudit\Tests\Feature\Console\Generators;

use Johannesclimacus\ModelAudit\Tests\Support\GeneratorTestCase;

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

    public function test_it_creates_a_migration_for_the_model(): void
    {
        $databasePath = $this->tmpAppPath . '/database';
        $migrationsPath = $databasePath . '/migrations';

        $this->filesystem->makeDirectory($migrationsPath, 0777, true);

        $this->app->useDatabasePath($databasePath);

        $this->artisan('make:auditable-model', [
            'name' => 'Invoice',
            '--migration' => true,
        ])->assertSuccessful();

        $this->assertFileExists($this->tmpAppPath . '/Models/Invoice.php');

        $migrationFiles = $this->filesystem->glob(
            $migrationsPath . '/*_create_invoices_table.php',
        );

        $this->assertCount(1, $migrationFiles);

        $contents = $this->filesystem->get($migrationFiles[0]);

        $this->assertStringContainsString("Schema::create('invoices'", $contents);
    }

    public function test_it_creates_a_migration_for_a_model_in_a_nested_namespace(): void
    {
        $databasePath = $this->tmpAppPath . '/database';
        $migrationsPath = $databasePath . '/migrations';

        $this->filesystem->makeDirectory($migrationsPath, 0777, true);

        $this->app->useDatabasePath($databasePath);

        $this->artisan('make:auditable-model', [
            'name' => 'Billing/InvoiceItem',
            '--migration' => true,
        ])->assertSuccessful();

        $this->assertFileExists($this->tmpAppPath . '/Models/Billing/InvoiceItem.php');

        $migrationFiles = $this->filesystem->glob(
            $migrationsPath . '/*_create_invoice_items_table.php',
        );

        $this->assertCount(1, $migrationFiles);

        $contents = $this->filesystem->get($migrationFiles[0]);

        $this->assertStringContainsString(
            "Schema::create('invoice_items'",
            $contents,
        );
    }
}
