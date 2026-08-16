<?php

namespace Johannesclimacus\ModelAudit\Console\Generators;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;

#[Signature('make:audit-ip-resolver
    {name : The name of the IP address resolver}
    {--f|force : Create class even if it already exists}
')]
#[Description('Create a new IP address resolver class')]
class MakeAuditIpResolverCommand extends MakeAuditClassCommand
{
    protected $type = 'Audit IP address resolver';

    protected string $stubName = 'ip-resolver.stub';

    protected string $namespaceSuffix = '\ModelAudit\Resolvers';
}
