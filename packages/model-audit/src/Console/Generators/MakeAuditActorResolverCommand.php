<?php

namespace Local\ModelAudit\Console\Generators;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;

#[Signature('make:audit-actor-resolver
    {name : The name of the actor resolver}
    {--f|force : Create class even if it already exists}
')]
#[Description('Create a new actor resolver class')]
class MakeAuditActorResolverCommand extends MakeAuditClassCommand
{
    protected $type = 'Audit actor resolver';

    protected string $stubName = 'actor-resolver.stub';

    protected string $namespaceSuffix = '\ModelAudit\Resolvers';
}
