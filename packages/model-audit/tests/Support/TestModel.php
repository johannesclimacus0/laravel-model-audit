<?php

namespace Johannesclimacus\ModelAudit\Tests\Support;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Johannesclimacus\ModelAudit\Traits\Auditable;

#[Fillable(['name', 'status'])]
class TestModel extends Model
{
    use Auditable;
}
