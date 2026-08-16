<?php

namespace Johannesclimacus\ModelAudit\Tests\Feature;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Johannesclimacus\ModelAudit\DTO\AuditLogQuery;
use Johannesclimacus\ModelAudit\History\DatabaseAuditLogReader;
use Johannesclimacus\ModelAudit\Models\AuditEntry;
use Johannesclimacus\ModelAudit\Tests\TestCase;

class DatabaseAuditLogReaderTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_filters_the_audit_log(): void
    {
        $matching = $this->createEntry([
            'subject_type' => 'invoice',
            'subject_id' => '10',
            'actor_type' => 'admin',
            'actor_id' => '3',
            'event' => 'approved',
            'request_id' => 'request-1',
            'created_at' => CarbonImmutable::parse('2026-08-15 12:00:00'),
        ]);

        foreach ([
            ['subject_type' => 'order'],
            ['subject_id' => '11'],
            ['actor_type' => 'service'],
            ['actor_id' => '4'],
            ['event' => 'created'],
            ['request_id' => 'request-2'],
            ['created_at' => CarbonImmutable::parse('2026-08-14 12:00:00')],
        ] as $attributes) {
            $this->createEntry($attributes);
        }

        $entries = (new DatabaseAuditLogReader)->paginate(
            new AuditLogQuery(
                event: 'approved',
                subjectType: 'invoice',
                subjectId: '10',
                actorType: 'admin',
                actorId: '3',
                requestId: 'request-1',
                dateFrom: CarbonImmutable::parse('2026-08-15 00:00:00'),
                dateTo: CarbonImmutable::parse('2026-08-15 23:59:59'),
            )
        );

        $this->assertCount(1, $entries);
        $this->assertSame($matching->uuid, $entries->first()->uuid);
    }

    public function test_it_returns_newest_entries_first_and_paginates_them(): void
    {
        $oldest = $this->createEntry(['event' => 'first']);
        $newest = $this->createEntry(['event' => 'second']);

        $entries = (new DatabaseAuditLogReader)->paginate(
            new AuditLogQuery(perPage: 1)
        );

        $this->assertSame(2, $entries->total());
        $this->assertCount(1, $entries);
        $this->assertSame($newest->uuid, $entries->first()->uuid);
        $this->assertNotSame($oldest->uuid, $entries->first()->uuid);
    }

    private function createEntry(array $attributes = []): AuditEntry
    {
        return AuditEntry::query()->create(array_merge([
            'subject_type' => 'invoice',
            'subject_id' => '10',
            'actor_type' => 'admin',
            'actor_id' => '3',
            'event' => 'approved',
            'old_values' => null,
            'new_values' => null,
            'metadata' => [],
            'request_id' => 'request-1',
            'created_at' => CarbonImmutable::parse('2026-08-15 12:00:00'),
        ], $attributes));
    }
}
