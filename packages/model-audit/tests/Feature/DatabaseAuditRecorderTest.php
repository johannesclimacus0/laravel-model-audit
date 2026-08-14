<?php

namespace Local\ModelAudit\Tests\Feature;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Local\ModelAudit\Contracts\AuditRecorder;
use Local\ModelAudit\DTO\AuditEntryData;
use Local\ModelAudit\Enums\ModelEvent;
use Local\ModelAudit\Models\AuditEntry;
use Local\ModelAudit\Recorders\DatabaseAuditRecorder;
use Local\ModelAudit\Tests\Support\TestModel;
use Local\ModelAudit\Tests\TestCase;

class DatabaseAuditRecorderTest extends TestCase
{
    use RefreshDatabase;

    public function test_container_resolves_database_recorder(): void
    {
        $recorder = $this->app->make(AuditRecorder::class);

        $this->assertInstanceOf(DatabaseAuditRecorder::class, $recorder);
        $this->assertSame($recorder, $this->app->make(AuditRecorder::class));
    }

    public function test_it_records_audit_data(): void
    {
        $subject = $this->persistedModel(101);
        $actor = $this->persistedModel(202);

        $data = new AuditEntryData(
            subject: $subject,
            event: ModelEvent::Updated,
            actor: $actor,
            oldValues: ['status' => 'pending'],
            newValues: ['status' => 'approved'],
            metadata: ['source' => 'test'],
            ipAddress: '127.0.0.1',
            userAgent: 'PHPUnit',
            requestId: 'request123',
            createdAt: CarbonImmutable::parse(
                '2026-08-11 08:00:00.123456',
                'UTC',
            )
        );

        $entry = $this->app->make(AuditRecorder::class)->record($data);

        $this->assertInstanceOf(AuditEntry::class, $entry);

        $this->assertSame('updated', $entry->event);
        $this->assertSame('101', $entry->subject_id);
        $this->assertSame('202', $entry->actor_id);
        $this->assertSame(['status' => 'pending'], $entry->old_values);
        $this->assertSame(['status' => 'approved'], $entry->new_values);
        $this->assertSame(['source' => 'test'], $entry->metadata);
        $this->assertSame('2026-08-11 08:00:00.123456', $entry->created_at->format('Y-m-d H:i:s.u'));

        $this->assertDatabaseHas('audit_entries', [
            'uuid' => $entry->uuid,
            'subject_type' => $subject->getMorphClass(),
            'subject_id' => '101',
            'actor_type' => $actor->getMorphClass(),
            'actor_id' => '202',
            'event' => 'updated',
            'request_id' => 'request123',
        ]);
    }

    public function test_it_does_nothing_when_disabled(): void
    {
        config()->set('model-audit.enabled', false);

        $result = $this->app->make(AuditRecorder::class)
            ->record(new AuditEntryData(
                subject: $this->persistedModel(101),
                event: ModelEvent::Created,
            ));

        $this->assertNull($result);
        $this->assertDatabaseCount('audit_entries', 0);
    }

    public function test_it_rejects_an_unpersisted_subject(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->expectExceptionMessage('Subject must have a primary key.');

        $this->app->make(AuditRecorder::class)
            ->record(new AuditEntryData(
                subject: new TestModel,
                event: ModelEvent::Created
            ));
    }

    private function persistedModel(int $id): TestModel
    {
        $model = new TestModel;
        $model->setAttribute($model->getKeyName(), $id);
        $model->exists = true;

        return $model;
    }
}
