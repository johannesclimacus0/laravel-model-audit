<?php

namespace Johannesclimacus\ModelAudit\Contracts;

interface UserAgentResolver
{
    public function resolve(): ?string;
}
