<?php

namespace Local\ModelAudit\Observers;

use Illuminate\Database\Eloquent\Model;
use Local\ModelAudit\Contracts\ActorResolver;
use Local\ModelAudit\Contracts\AuditAttributeFilter;
use Local\ModelAudit\Contracts\AuditRecorder;
use Local\ModelAudit\Contracts\AuditValueMasker;
use Local\ModelAudit\DTO\AuditEntryData;
use Local\ModelAudit\Enums\ModelEvent;
use Local\ModelAudit\Contracts\IpAddressResolver;
use Local\ModelAudit\Contracts\RequestIdResolver;
use Local\ModelAudit\Contracts\UserAgentResolver;
use Local\ModelAudit\Contracts\AuditLogger;

class AuditableObserver
{
    public function __construct(
        private AuditRecorder $recorder,
        private AuditAttributeFilter $filter,
        private AuditValueMasker $mask,
        private AuditLogger $logger,
    ) {}

    public function created(Model $model): void
    {
        $newValues = $this->filter->filter($model, $model->getAttributes());
        $newValues = $this->mask->mask($model, $newValues);

        $this->logger->record(
            subject: $model,
            event: ModelEvent::Created,
            newValues: $newValues,
        );
    }

    public function updated(Model $model): void
    {
        $newValues = $this->filter->filter($model, $model->getChanges());

        if ($newValues === []) {
            return;
        }
        $previous = $model->getPrevious();

        $oldValues = array_intersect_key($previous, $newValues);

        $newValues = $this->mask->mask($model, $newValues);
        $oldValues = $this->mask->mask($model, $oldValues);

        $this->logger->record(
            subject: $model,
            event: ModelEvent::Updated,
            oldValues: $oldValues,
            newValues: $newValues,
        );
    }

    public function deleted(Model $model): void
    {
        $oldValues = $this->filter->filter($model, $model->getAttributes());
        $oldValues = $this->mask->mask($model, $oldValues);

        $this->logger->record(
            subject: $model,
            event: ModelEvent::Deleted,
            oldValues: $oldValues,
        );
    }

    public function restored(Model $model): void
    {
        $newValues = $this->filter->filter($model, $model->getAttributes());

        $newValues = $this->mask->mask($model, $newValues);

        $this->logger->record(
            subject: $model,
            event: ModelEvent::Restored,
            newValues: $newValues,
        );
    }
}
