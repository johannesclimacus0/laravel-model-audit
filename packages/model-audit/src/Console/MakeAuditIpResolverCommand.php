<?php

namespace Local\ModelAudit\Console;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\GeneratorCommand;

#[Signature('make:audit-ip-resolver
    {name : The name of the IP address resolver}
    {--f|force : Create class even if it already exists}
')]
#[Description('Create a new IP address resolver class')]
class MakeAuditIpResolverCommand extends GeneratorCommand
{
    protected $type = 'Audit IP address resolver';

    public function handle(): int
    {
        return parent::handle() === false
            ? self::FAILURE
            : self::SUCCESS;
    }

    protected function getStub(): string
    {
        return __DIR__ . '/../../stubs/ip-resolver.stub';
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\ModelAudit\Resolvers';
    }
}
