<?php

namespace Local\ModelAudit\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Local\ModelAudit\Contracts\AuditLogger;
use Local\ModelAudit\Models\AuditEntry;
use Local\ModelAudit\Tests\Support\TestModel;
use Local\ModelAudit\Tests\TestCase;

class DefaultAuditLoggerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_records_a_custom_audit_event(): void
    {
        $model = TestModel::query()->create([
            'name' => 'Test invoice',
            'status' => 'pending',
        ]);

        $logger = $this->app->make(AuditLogger::class);

        $entry = $logger->record(
            subject: $model,
            event: 'invoice.approved',
            metadata: [
                'reason' => 'approval',
            ],
        );

        $this->assertInstanceOf(AuditEntry::class, $entry);
        $this->assertSame('invoice.approved', $entry->event);
        $this->assertSame($model->getMorphClass(), $entry->subject_type);
        $this->assertSame((string) $model->getKey(), $entry->subject_id);
        $this->assertSame(['reason' => 'approval'], $entry->metadata);
        $this->assertNull($entry->old_values);
        $this->assertNull($entry->new_values);

        $this->assertDatabaseHas('audit_entries', [
            'uuid' => $entry->uuid,
            'event' => 'invoice.approved',
            'subject_type' => $model->getMorphClass(),
            'subject_id' => (string) $model->getKey(),
        ]);
    }
}
