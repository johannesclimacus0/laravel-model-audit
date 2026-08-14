<?php

namespace Local\ModelAudit\Contracts;

interface RequestIdResolver
{
    public function resolve(): ?string;
}
