<?php

namespace Local\ModelAudit\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Local\ModelAudit\DTO\AuditHistoryQuery;
use Local\ModelAudit\History\DatabaseAuditHistoryReader;
use Local\ModelAudit\Models\AuditEntry;
use Local\ModelAudit\Tests\Support\TestModel;
use Local\ModelAudit\Tests\TestCase;

class DatabaseAuditHistoryReaderTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_reads_history_only_for_the_requested_subject(): void
    {
        $firstModel = TestModel::query()->create([
            'name' => 'First invoice',
            'status' => 'pending',
        ]);

        TestModel::query()->create([
            'name' => 'Second invoice',
            'status' => 'pending',
        ]);

        $firstModel->update(['status' => 'approved']);

        $query = new AuditHistoryQuery(
            subjectType: $firstModel->getMorphClass(),
            subjectId: (string) $firstModel->getKey(),
        );

        $entries = (new DatabaseAuditHistoryReader)->read($query);

        $this->assertCount(2, $entries);

        $this->assertTrue(
            $entries->every(
                fn (AuditEntry $entry): bool =>
                    $entry->subject_type === $query->subjectType
                    && $entry->subject_id === $query->subjectId
            )
        );
    }

    public function test_it_returns_newest_entries_first(): void
    {
        $model = TestModel::query()->create([
            'name' => 'Test invoice',
            'status' => 'pending',
        ]);

        $model->update([
            'status' => 'approved',
        ]);

        $query = new AuditHistoryQuery(
            subjectType: $model->getMorphClass(),
            subjectId: (string) $model->getKey(),
        );

        $entries = (new DatabaseAuditHistoryReader)->read($query);

        $this->assertSame(
            ['updated', 'created'],
            $entries->pluck('event')->all(),
        );
    }
}
