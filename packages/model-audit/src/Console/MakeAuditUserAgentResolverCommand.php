<?php

namespace Local\ModelAudit\Console;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\GeneratorCommand;

#[Signature('make:audit-user-agent-resolver
    {name : The name of the user agent resolver}
    {--f|force : Create class even if it already exists}
')]
#[Description('Create a new user agent resolver class')]
class MakeAuditUserAgentResolverCommand extends GeneratorCommand
{
    protected $type = 'Audit user agent resolver';

    public function handle(): int
    {
        return parent::handle() === false
            ? self::FAILURE
            : self::SUCCESS;
    }

    protected function getStub(): string
    {
        return __DIR__ . '/../../stubs/user-agent-resolver.stub';
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\ModelAudit\Resolvers';
    }
}
