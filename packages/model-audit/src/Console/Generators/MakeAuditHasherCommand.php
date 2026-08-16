<?php

namespace Johannesclimacus\ModelAudit\Console\Generators;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;

#[Signature('make:audit-hasher
    {name : The name of the audit hasher}
    {--f|force : Create class even if it already exists}
')]
#[Description('Create a new audit hasher class')]
class MakeAuditHasherCommand extends MakeAuditClassCommand
{
    protected $type = 'Audit hasher';

    protected string $stubName = 'audit-hasher.stub';

    protected string $namespaceSuffix = '\ModelAudit\Hashing';
}
