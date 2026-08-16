<?php

namespace Johannesclimacus\ModelAudit\Http\Controllers;

use Carbon\CarbonImmutable;
use Illuminate\View\View;
use Johannesclimacus\ModelAudit\Contracts\AuditChainVerifier;
use Johannesclimacus\ModelAudit\Contracts\AuditHistoryReader;
use Johannesclimacus\ModelAudit\Contracts\AuditLogReader;
use Johannesclimacus\ModelAudit\DTO\AuditHistoryQuery;
use Johannesclimacus\ModelAudit\DTO\AuditLogQuery;
use Johannesclimacus\ModelAudit\Http\Requests\AuditIndexRequest;
use Johannesclimacus\ModelAudit\Models\AuditEntry;

class AuditController
{
    public function __construct(
        private AuditLogReader $logReader,
        private AuditHistoryReader $historyReader,
        private AuditChainVerifier $chainVerifier,
    )
    {
    }

    public function index(AuditIndexRequest $request): View
    {
        $filters = $request->validated();

        $timezone = (string) config('app.timezone', 'UTC');
        $perPage = min(100, max(1, (int) config('model-audit.ui.per_page', 25)));

        $query = new AuditLogQuery(
            event: $filters['event'] ?? null,
            subjectType: $filters['subject_type'] ?? null,
            subjectId: $filters['subject_id'] ?? null,
            actorType: $filters['actor_type'] ?? null,
            actorId: $filters['actor_id'] ?? null,
            requestId: $filters['request_id'] ?? null,
            dateFrom: isset($filters['date_from'])
                ? CarbonImmutable::parse($filters['date_from'], $timezone)->startOfDay()->utc()
                : null,
            dateTo: isset($filters['date_to'])
                ? CarbonImmutable::parse($filters['date_to'], $timezone)->endOfDay()->utc()
                : null,
            perPage: $perPage,
        );

        $entries = $this->logReader
            ->paginate($query)
            ->withQueryString();

        return view('model-audit::index', [
            'entries' => $entries,
        ]);
    }

    public function show(AuditEntry $audit): View
    {
        return view('model-audit::show', [
            'entry' => $audit,
        ]);
    }

    public function subject(string $type, string $id): View
    {
        $limit = min(100, max(1, (int) config('model-audit.ui.subject_limit', 100)));

        $query = new AuditHistoryQuery(
            subjectType: $type,
            subjectId: $id,
            limit: $limit,
        );

        return view('model-audit::subject', [
            'query' => $query,
            'entries' => $this->historyReader->read($query),
            'integrity' => $this->chainVerifier->verify(
                $query->subjectType,
                $query->subjectId,
            ),
        ]);
    }
}
