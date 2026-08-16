<?php

namespace Johannesclimacus\ModelAudit\DTO;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Johannesclimacus\ModelAudit\Enums\ModelEvent;

class AuditEntryData
{
    public string $event;

    public CarbonImmutable $createdAt;

    public function __construct(
        public Model $subject,
        string|ModelEvent $event,
        public ?Model $actor = null,
        public ?array $oldValues = null,
        public ?array $newValues = null,
        public array $metadata = [],
        public ?string $ipAddress = null,
        public ?string $userAgent = null,
        public ?string $requestId = null,
        ?CarbonImmutable $createdAt = null,
    ) {
        $event = $event instanceof ModelEvent ? $event->value : trim($event);

        if ($event === '') {
            throw new InvalidArgumentException('Audit event cannot be empty.');
        }

        $this->event = $event;

        $this->createdAt = ($createdAt ?? CarbonImmutable::now('UTC'))->utc();
    }
}
