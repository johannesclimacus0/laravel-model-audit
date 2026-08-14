<?php

namespace Local\ModelAudit\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
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
    public function test_it_does_not_capture_disabled_request_context(): void
    {
        config()->set('model-audit.context.ip_address', false);
        config()->set('model-audit.context.user_agent', false);
        config()->set('model-audit.context.request_id', false);

        $request = Request::create('/', 'GET', server: [
            'REMOTE_ADDR' => '203.0.113.10',
            'HTTP_USER_AGENT' => 'Chrome',
        ]);

        $this->app->instance('request', $request);

        $model = TestModel::query()->create([
            'name' => 'Test invoice',
            'status' => 'pending',
        ]);

        $logger = $this->app->make(AuditLogger::class);

        $entry = $logger->record(
            subject: $model,
            event: 'invoice.approved',
        );

        $this->assertInstanceOf(AuditEntry::class, $entry);
        $this->assertNull($entry->ip_address);
        $this->assertNull($entry->user_agent);
        $this->assertNull($entry->request_id);
    }
}
