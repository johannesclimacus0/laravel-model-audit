<?php

namespace Johannesclimacus\ModelAudit\Logging;

use Illuminate\Database\Eloquent\Model;
use Johannesclimacus\ModelAudit\Contracts\ActorResolver;
use Johannesclimacus\ModelAudit\Contracts\AuditLogger;
use Johannesclimacus\ModelAudit\Contracts\AuditRecorder;
use Johannesclimacus\ModelAudit\Contracts\IpAddressResolver;
use Johannesclimacus\ModelAudit\Contracts\RequestIdResolver;
use Johannesclimacus\ModelAudit\Contracts\UserAgentResolver;
use Johannesclimacus\ModelAudit\DTO\AuditEntryData;
use Johannesclimacus\ModelAudit\Enums\ModelEvent;
use Johannesclimacus\ModelAudit\Models\AuditEntry;

class DefaultAuditLogger implements AuditLogger
{
    public function __construct(
        private AuditRecorder $recorder,
        private ActorResolver $actorResolver,
        private IpAddressResolver $ipAddressResolver,
        private UserAgentResolver $userAgentResolver,
        private RequestIdResolver $requestIdResolver,
    ) {}

    public function record(
        Model $subject,
        ModelEvent|string $event,
        array $metadata = [],
        ?array $oldValues = null,
        ?array $newValues = null,
    ): ?AuditEntry {
        $data = new AuditEntryData(
            subject: $subject,
            event: $event,
            actor: $this->actorResolver->resolve(),
            oldValues: $oldValues,
            newValues: $newValues,
            metadata: $metadata,
            ipAddress: config('model-audit.context.ip_address', true)
                ? $this->ipAddressResolver->resolve()
                : null,
            userAgent: config('model-audit.context.user_agent', true)
                ? $this->userAgentResolver->resolve()
                : null,
            requestId: config('model-audit.context.request_id', true)
                ? $this->requestIdResolver->resolve()
                : null,
        );

        return $this->recorder->record($data);
    }
}
