<?php

namespace Johannesclimacus\ModelAudit\DTO;

use InvalidArgumentException;

class AuditHistoryQuery
{
    public const DEFAULT_LIMIT = 20;

    public const MAX_LIMIT = 100;

    public string $subjectType;

    public string $subjectId;

    public ?string $event;

    public function __construct(
        string $subjectType,
        string $subjectId,
        ?string $event = null,
        public int $limit = self::DEFAULT_LIMIT,
    )
    {
        $this->subjectType = trim($subjectType);
        $this->subjectId = trim($subjectId);

        $event = $event === null ? null : trim($event);
        $this->event = $event === '' ? null : $event;

        if ($this->subjectType === '') {
            throw new InvalidArgumentException('Subject type cannot be empty.');
        }

        if ($this->subjectId === '') {
            throw new InvalidArgumentException('Subject ID cannot be empty.');
        }

        if ($this->limit < 1 || $this->limit > self::MAX_LIMIT) {
            throw new InvalidArgumentException(
                'History limit must be between 1 and ' . self::MAX_LIMIT . '.'
            );
        }
    }
}
