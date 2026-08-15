<?php

namespace Local\ModelAudit\Console\Generators;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;

#[Signature('make:audit-request-id-resolver
    {name : The name of the request ID resolver}
    {--f|force : Create class even if it already exists}
')]
#[Description('Create a new request ID resolver class')]
class MakeAuditRequestIdResolverCommand extends MakeAuditClassCommand
{
    protected $type = 'Audit request ID resolver';

    protected string $stubName = 'request-id-resolver.stub';

    protected string $namespaceSuffix = '\ModelAudit\Resolvers';
}
