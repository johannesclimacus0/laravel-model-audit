<?php

namespace Johannesclimacus\ModelAudit\DTO;

use Carbon\CarbonImmutable;
use InvalidArgumentException;

class AuditLogQuery
{
    public ?string $event;

    public ?string $subjectType;

    public ?string $subjectId;

    public ?string $actorType;

    public ?string $actorId;

    public ?string $requestId;

    public function __construct(
        ?string $event = null,
        ?string $subjectType = null,
        ?string $subjectId = null,
        ?string $actorType = null,
        ?string $actorId = null,
        ?string $requestId = null,
        public ?CarbonImmutable $dateFrom = null,
        public ?CarbonImmutable $dateTo = null,
        public int $perPage = 25,
    ) {
        $this->event = $this->normalize($event);
        $this->subjectType = $this->normalize($subjectType);
        $this->subjectId = $this->normalize($subjectId);
        $this->actorType = $this->normalize($actorType);
        $this->actorId = $this->normalize($actorId);
        $this->requestId = $this->normalize($requestId);

        if ($this->perPage < 1 || $this->perPage > 100) {
            throw new InvalidArgumentException(
                'Entries per page must be between 1 and 100.'
            );
        }

        if ($this->dateFrom !== null
            && $this->dateTo !== null
            && $this->dateFrom->isAfter($this->dateTo)) {
            throw new InvalidArgumentException(
                'The start date must not be after the end date.'
            );
        }
    }

    private function normalize(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }
}
