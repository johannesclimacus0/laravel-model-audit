<?php

namespace Local\ModelAudit\Console;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\GeneratorCommand;

#[Signature('make:audit-hasher
    {name : The name of the audit hasher}
    {--f|force : Create class even if it already exists}
')]
#[Description('Create a new audit hasher class')]
class MakeAuditHasherCommand extends GeneratorCommand
{
    protected $type = 'Audit hasher';

    public function handle(): int
    {
        return parent::handle() === false
            ? self::FAILURE
            : self::SUCCESS;
    }

    protected function getStub(): string
    {
        return __DIR__ . '/../../stubs/audit-hasher.stub';
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\ModelAudit\Hashing';
    }
}
