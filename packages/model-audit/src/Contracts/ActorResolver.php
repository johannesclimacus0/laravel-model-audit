<?php

namespace Johannesclimacus\ModelAudit\Contracts;

use Illuminate\Database\Eloquent\Model;

interface ActorResolver
{
    public function resolve(): ?Model;
}
