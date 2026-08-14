<?php

namespace Local\ModelAudit\Contracts;

interface IpAddressResolver
{
    public function resolve(): ?string;
}
