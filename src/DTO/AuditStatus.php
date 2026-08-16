<?php

namespace Johannesclimacus\ModelAudit\DTO;

use Carbon\CarbonImmutable;

class AuditStatus
{
    public function __construct(
        public bool $enabled,
        public string $connectionName,
        public string $entriesTable,
        public string $chainStatesTable,
        public int $entriesCount,
        public int $chainsCount,
        public ?CarbonImmutable $lastEntryAt,
    ) {}
}
