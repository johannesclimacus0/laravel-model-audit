<?php

namespace Local\ModelAudit\Console\Generators;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;

#[Signature('make:audit-filter
    {name : The name of the audit attribute filter}
    {--f|force : Create class even if it already exists}
')]
#[Description('Create a new audit attribute filter class')]
class MakeAuditFilterCommand extends MakeAuditClassCommand
{
    protected $type = 'Audit attribute filter';

    protected string $stubName = 'audit-filter.stub';

    protected string $namespaceSuffix = '\ModelAudit\Filtering';
}
