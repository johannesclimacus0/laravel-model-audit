<?php

namespace Johannesclimacus\ModelAudit\Console\Generators;

use Illuminate\Console\GeneratorCommand;

abstract class MakeAuditClassCommand extends GeneratorCommand
{
    protected string $stubName;

    protected string $namespaceSuffix;

    public function handle(): int
    {
        if (parent::handle() === false) {
            return self::FAILURE;
        }

        return $this->afterGenerated();
    }

    protected function afterGenerated(): int
    {
        return self::SUCCESS;
    }

    protected function getStub(): string
    {
        return __DIR__ . '/../../../stubs/' . $this->stubName;
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . $this->namespaceSuffix;
    }
}
