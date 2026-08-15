<?php

namespace Local\ModelAudit\Console;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\GeneratorCommand;

#[Signature('make:audit-actor-resolver
    {name : The name of the actor resolver}
    {--f|force : Create class even if it already exists}
')]
#[Description('Create a new actor resolver class')]
class MakeAuditActorResolverCommand extends GeneratorCommand
{
    protected $type = 'Audit actor resolver';

    public function handle(): int
    {
        return parent::handle() === false
            ? self::FAILURE
            : self::SUCCESS;
    }

    protected function getStub(): string
    {
        return __DIR__ . '/../../stubs/actor-resolver.stub';
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\ModelAudit\Resolvers';
    }
}
