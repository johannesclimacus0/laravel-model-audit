<?php

namespace Johannesclimacus\ModelAudit\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use JsonException;
use Johannesclimacus\ModelAudit\Contracts\AuditHistoryReader;
use Johannesclimacus\ModelAudit\DTO\AuditHistoryQuery;
use Johannesclimacus\ModelAudit\Models\AuditEntry;

#[Signature('model-audit:show
    {subjectType : Stored morph type}
    {subjectId : Subject primary key}
    {--event= : Only show entries for this event}
    {--limit=20 : Maximum number of entries}
    {--json : Output history as JSON}
')]
#[Description('Display audit history for a subject')]
class ShowAuditHistoryCommand extends Command
{
    public function __construct(
        private AuditHistoryReader $reader,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $limit = filter_var($this->option('limit'), FILTER_VALIDATE_INT);

        if ($limit === false) {
            $this->error(
                __('model-audit::commands.show.invalid_limit_integer')
            );

            return self::INVALID;
        }

        if ($limit < 1 || $limit > AuditHistoryQuery::MAX_LIMIT) {
            $this->error(
                __('model-audit::commands.show.invalid_limit_range', [
                    'max' => AuditHistoryQuery::MAX_LIMIT,
                ])
            );

            return self::INVALID;
        }

        $subjectType = trim((string) $this->argument('subjectType'));
        $subjectId = trim((string) $this->argument('subjectId'));

        if ($subjectType === '') {
            $this->error(
                __('model-audit::commands.show.subject_type_required')
            );

            return self::INVALID;
        }

        if ($subjectId === '') {
            $this->error(
                __('model-audit::commands.show.subject_id_required')
            );

            return self::INVALID;
        }

        $event = $this->option('event');

        $query = new AuditHistoryQuery(
            subjectType: $subjectType,
            subjectId: $subjectId,
            event: is_string($event) ? $event : null,
            limit: $limit,
        );

        $entries = $this->reader->read($query);

        if ($this->option('json')) {
            return $this->outputJson($entries);
        }

        if ($entries->isEmpty()) {
            $this->info(
                __('model-audit::commands.show.none')
            );

            return self::SUCCESS;
        }

        $this->components->info(
            __('model-audit::commands.show.title', [
                'subject_type' => $query->subjectType,
                'subject_id' => $query->subjectId,
            ])
        );

        $this->table(
            [
                __('model-audit::commands.show.headers.uuid'),
                __('model-audit::commands.show.headers.event'),
                __('model-audit::commands.show.headers.actor'),
                __('model-audit::commands.show.headers.created_at'),
            ],
            $entries
                ->map(fn (AuditEntry $entry): array => [
                    $entry->uuid,
                    $entry->event,
                    $this->formatActor($entry),
                    $entry->created_at?->toIso8601String(),
                ])
                ->all(),
            'box-double',
        );

        return self::SUCCESS;
    }

    private function formatActor(AuditEntry $entry): string
    {
        if ($entry->actor_type === null || $entry->actor_id === null) {
            return __('model-audit::commands.show.system');
        }

        return $entry->actor_type . ' [' . $entry->actor_id . ']';
    }

    /**
     * @param Collection<int, AuditEntry> $entries
     */
    private function outputJson(Collection $entries): int
    {
        $data = $entries
            ->map(fn (AuditEntry $entry): array => [
                'uuid' => $entry->uuid,
                'subject_type' => $entry->subject_type,
                'subject_id' => $entry->subject_id,
                'actor_type' => $entry->actor_type,
                'actor_id' => $entry->actor_id,
                'event' => $entry->event,
                'old_values' => $entry->old_values,
                'new_values' => $entry->new_values,
                'metadata' => $entry->metadata,
                'ip_address' => $entry->ip_address,
                'user_agent' => $entry->user_agent,
                'request_id' => $entry->request_id,
                'previous_hash' => $entry->previous_hash,
                'hash' => $entry->hash,
                'created_at' => $entry->created_at?->toIso8601String(),
            ])
            ->values()
            ->all();

        try {
            $json = json_encode(
                $data,
                JSON_PRETTY_PRINT
                    | JSON_UNESCAPED_UNICODE
                    | JSON_UNESCAPED_SLASHES
                    | JSON_THROW_ON_ERROR,
            );
        } catch (JsonException) {
            $this->error(
                __('model-audit::commands.show.json_encoding_failed')
            );

            return self::FAILURE;
        }

        $this->line($json);

        return self::SUCCESS;
    }
}
