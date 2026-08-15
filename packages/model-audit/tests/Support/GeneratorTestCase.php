<?php

namespace Local\ModelAudit\Tests\Support;

use Illuminate\Filesystem\Filesystem;
use Local\ModelAudit\Tests\TestCase;

abstract class GeneratorTestCase extends TestCase
{
    protected Filesystem $filesystem;

    protected string $tmpAppPath;

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

    protected function assertGeneratorCreates(
        string $command,
        string $name,
        string $relativePath,
        array $expectedContents
    ): void {
        $this->artisan($command, [
            'name' => $name,
        ])->assertSuccessful();

        $path = $this->tmpAppPath . $relativePath;

        $this->assertFileExists($path);

        $contents = $this->filesystem->get($path);
        $namespace = trim($this->app->getNamespace(), '\\');

        $this->assertStringContainsString(
            'namespace ' . $namespace . str_replace('/', '\\', dirname($relativePath)) . ';',
            $contents,
        );

        foreach ($expectedContents as $expectedContent) {
            $this->assertStringContainsString($expectedContent, $contents);
        }

        $this->assertStringNotContainsString('{{ class }}', $contents);
        $this->assertStringNotContainsString('{{ namespace }}', $contents);
    }
}
