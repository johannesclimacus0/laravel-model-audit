<?php

namespace Local\ModelAudit\Logging;

use Illuminate\Database\Eloquent\Model;
use Local\ModelAudit\Contracts\ActorResolver;
use Local\ModelAudit\Contracts\AuditLogger;
use Local\ModelAudit\Contracts\AuditRecorder;
use Local\ModelAudit\Contracts\IpAddressResolver;
use Local\ModelAudit\Contracts\RequestIdResolver;
use Local\ModelAudit\Contracts\UserAgentResolver;
use Local\ModelAudit\DTO\AuditEntryData;
use Local\ModelAudit\Enums\ModelEvent;
use Local\ModelAudit\Models\AuditEntry;

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
