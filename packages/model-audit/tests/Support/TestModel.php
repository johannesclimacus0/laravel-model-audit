<?php

namespace Local\ModelAudit\Tests\Support;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Local\ModelAudit\Traits\Auditable;

#[Fillable(['name', 'status'])]
class TestModel extends Model
{
    use Auditable;
}
