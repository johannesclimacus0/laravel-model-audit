<?php

namespace Johannesclimacus\ModelAudit\Tests\Support;

use Illuminate\Database\Eloquent\SoftDeletes;

class SoftDeletedModel extends TestModel
{
    use SoftDeletes;

    protected $table = 'test_models';
}
