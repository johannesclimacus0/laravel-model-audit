<?php

namespace Local\ModelAudit\Console;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\GeneratorCommand;

#[Signature('make:auditable-model
     {name : The name of the model}
     {--f|force : Create class even if it already exists}
')]
#[Description('Create a new auditable Eloquent model class')]
class MakeAuditableModelCommand extends GeneratorCommand
{
    protected $type = 'Auditable model';

    public function handle(): int
    {
        return parent::handle() === false
            ? self::FAILURE
            : self::SUCCESS;
    }

    protected function getStub(): string
    {
        return __DIR__ . '/../../stubs/auditable-model.stub';
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\Models';
    }
}
