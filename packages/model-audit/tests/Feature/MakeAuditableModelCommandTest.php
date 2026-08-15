<?php

namespace Local\ModelAudit\Tests\Feature;

use Illuminate\Filesystem\Filesystem;
use Local\ModelAudit\Tests\TestCase;

class MakeAuditableModelCommandTest extends TestCase
{
    private Filesystem $filesystem;

    private string $tmpAppPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->getNamespace();

        $this->filesystem = new Filesystem;

        $this->tmpAppPath = sys_get_temp_dir() . '/model-audit-gen-' . bin2hex(random_bytes(4));

        $this->filesystem->makeDirectory($this->tmpAppPath, 0777, true);

        $this->app->useAppPath($this->tmpAppPath);
    }

    protected function tearDown(): void
    {
        try {
            if (isset($this->filesystem, $this->tmpAppPath)) {
                $this->filesystem->deleteDirectory($this->tmpAppPath);
            }
        } finally {
            parent::tearDown();
        }
    }

    public function test_it_makes_auditable_model(): void
    {
        $this->artisan('make:auditable-model', [
            'name' => 'Invoice',
        ])->assertSuccessful();

        $path = $this->tmpAppPath . '/Models/Invoice.php';

        $this->assertFileExists($path);

        $contents = $this->filesystem->get($path);

        $namespace = trim($this->app->getNamespace(), '\\');

        $this->assertStringContainsString(
            'namespace ' . $namespace . '\Models;',
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

        $this->assertStringContainsString(
            'protected array $auditInclude = [];',
            $contents,
        );

        $this->assertStringNotContainsString(
            '{{ class }}',
            $contents,
        );
    }

    public function test_it_creates_a_model_in_a_nested_namespace(): void
    {
        $this->artisan('make:auditable-model', [
            'name' => 'Billing/Invoice',
        ])->assertSuccessful();

        $path = $this->tmpAppPath . '/Models/Billing/Invoice.php';

        $this->assertFileExists($path);

        $contents = $this->filesystem->get($path);
        $namespace = trim($this->app->getNamespace(), '\\');

        $this->assertStringContainsString(
            'namespace ' . $namespace . '\\Models\\Billing;',
            $contents,
        );

        $this->assertStringContainsString(
            'class Invoice extends Model',
            $contents,
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
