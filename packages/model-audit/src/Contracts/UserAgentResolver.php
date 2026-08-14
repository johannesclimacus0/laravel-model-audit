<?php

namespace Local\ModelAudit\Contracts;

interface UserAgentResolver
{
    public function resolve(): ?string;
}
