<?php

namespace Johannesclimacus\ModelAudit\Contracts;

interface RequestIdResolver
{
    public function resolve(): ?string;
}
