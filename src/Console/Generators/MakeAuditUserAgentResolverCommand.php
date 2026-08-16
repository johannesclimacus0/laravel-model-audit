<?php

namespace Johannesclimacus\ModelAudit\Console\Generators;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;

#[Signature('make:audit-user-agent-resolver
    {name : The name of the user agent resolver}
    {--f|force : Create class even if it already exists}
')]
#[Description('Create a new user agent resolver class')]
class MakeAuditUserAgentResolverCommand extends MakeAuditClassCommand
{
    protected $type = 'Audit user agent resolver';

    protected string $stubName = 'user-agent-resolver.stub';

    protected string $namespaceSuffix = '\ModelAudit\Resolvers';
}
