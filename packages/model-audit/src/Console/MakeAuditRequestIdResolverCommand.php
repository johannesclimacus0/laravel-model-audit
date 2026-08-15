<?php

namespace Local\ModelAudit\Console;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\GeneratorCommand;

#[Signature('make:audit-request-id-resolver
    {name : The name of the request ID resolver}
    {--f|force : Create class even if it already exists}
')]
#[Description('Create a new request ID resolver class')]
class MakeAuditRequestIdResolverCommand extends GeneratorCommand
{
    protected $type = 'Audit request ID resolver';

    public function handle(): int
    {
        return parent::handle() === false
            ? self::FAILURE
            : self::SUCCESS;
    }

    protected function getStub(): string
    {
        return __DIR__ . '/../../stubs/request-id-resolver.stub';
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\ModelAudit\Resolvers';
    }
}
