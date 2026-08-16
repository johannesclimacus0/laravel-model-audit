<?php

namespace Johannesclimacus\ModelAudit\Tests\Support;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Foundation\Auth\User;

#[Fillable(['name', 'status'])]
class TestUser extends User
{
    protected $table = 'test_models';
}
