<?php

namespace Johannesclimacus\ModelAudit\Tests\Support;

class ExcludedFieldsModel extends TestModel
{
    protected $table = 'test_models';

    public function auditExclude(): array
    {
        return ['name'];
    }
}
