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

class AuditableObserver
{
    public function __construct(
        private AuditRecorder $recorder,
        private AuditAttributeFilter $filter,
        private AuditValueMasker $mask,
        private ActorResolver $actorResolver,
        private IpAddressResolver $ipAddressResolver,
        private UserAgentResolver $userAgentResolver,
        private RequestIdResolver $requestIdResolver,
    ) {}

    public function created(Model $model): void
    {
        $newValues = $this->filter->filter($model, $model->getAttributes());
        $newValues = $this->mask->mask($model, $newValues);

        $this->recorder->record(
            new AuditEntryData(
                subject: $model,
                event: ModelEvent::Created,
                actor: $this->actorResolver->resolve(),
                newValues: $newValues,
                ipAddress: $this->ipAddressResolver->resolve(),
                userAgent: $this->userAgentResolver->resolve(),
                requestId: $this->requestIdResolver->resolve(),
            )
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

        $this->recorder->record(
            new AuditEntryData(
                subject: $model,
                event: ModelEvent::Updated,
                actor: $this->actorResolver->resolve(),
                oldValues: $oldValues,
                newValues: $newValues,
                ipAddress: $this->ipAddressResolver->resolve(),
                userAgent: $this->userAgentResolver->resolve(),
                requestId: $this->requestIdResolver->resolve(),
            )
        );
    }

    public function deleted(Model $model): void
    {
        $oldValues = $this->filter->filter($model, $model->getAttributes());
        $oldValues = $this->mask->mask($model, $oldValues);

        $this->recorder->record(
            new AuditEntryData(
                subject: $model,
                event: ModelEvent::Deleted,
                actor: $this->actorResolver->resolve(),
                oldValues: $oldValues,
                newValues: null,
                ipAddress: $this->ipAddressResolver->resolve(),
                userAgent: $this->userAgentResolver->resolve(),
                requestId: $this->requestIdResolver->resolve(),
            )
        );
    }

    public function restored(Model $model): void
    {
        $newValues = $this->filter->filter($model, $model->getAttributes());

        $newValues = $this->mask->mask($model, $newValues);

        $this->recorder->record(
            new AuditEntryData(
                subject: $model,
                event: ModelEvent::Restored,
                actor: $this->actorResolver->resolve(),
                oldValues: null,
                newValues: $newValues,
                ipAddress: $this->ipAddressResolver->resolve(),
                userAgent: $this->userAgentResolver->resolve(),
                requestId: $this->requestIdResolver->resolve(),
            )
        );
    }
}
