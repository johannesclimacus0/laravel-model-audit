<?php

namespace Local\ModelAudit\DTO;

class AuditChainIdentifier
{
    public function __construct(
        public string $subjectType,
        public string $subjectId,
    ) {}
}
