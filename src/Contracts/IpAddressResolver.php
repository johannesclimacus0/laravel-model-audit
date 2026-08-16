<?php

namespace Johannesclimacus\ModelAudit\Contracts;

interface IpAddressResolver
{
    public function resolve(): ?string;
}
