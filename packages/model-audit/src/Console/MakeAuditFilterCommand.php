<?php

namespace Local\ModelAudit\Console;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\GeneratorCommand;

#[Signature('make:audit-filter
    {name : The name of the audit attribute filter}
    {--f|force : Create class even if it already exists}
')]
#[Description('Create a new audit attribute filter class')]
class MakeAuditFilterCommand extends GeneratorCommand
{
    protected $type = 'Audit attribute filter';

    public function handle(): int
    {
        return parent::handle() === false
            ? self::FAILURE
            : self::SUCCESS;
    }

    protected function getStub(): string
    {
        return __DIR__ . '/../../stubs/audit-filter.stub';
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\ModelAudit\Filtering';
    }
}
