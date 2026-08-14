<?php

namespace Local\ModelAudit\Tests\Unit;

use Carbon\CarbonImmutable;
use Local\ModelAudit\DTO\AuditEntryData;
use Local\ModelAudit\Enums\ModelEvent;
use Local\ModelAudit\Payloads\DefaultAuditPayloadBuilder;
use Local\ModelAudit\Tests\Support\TestModel;
use Local\ModelAudit\Tests\TestCase;

class DefaultAuditPayloadBuilderTest extends TestCase
{
    public function test_it_builds_a_versioned_payload_from_audit_data(): void
    {
        $subject = $this->persistedModel(101);
        $actor = $this->persistedModel(202);
        $previousHash = str_repeat('a', 64);

        $data = new AuditEntryData(
            subject: $subject,
            event: ModelEvent::Updated,
            actor: $actor,
            oldValues: ['status' => 'pending'],
            newValues: ['status' => 'approved'],
            metadata: ['source' => 'test'],
            ipAddress: '203.0.113.10',
            userAgent: 'PHPUnit',
            requestId: 'request-123',
            createdAt: CarbonImmutable::parse(
                '2026-08-11 18:00:00.123456',
                'Australia/Sydney',
            ),
        );

        $payload = (new DefaultAuditPayloadBuilder)->build(
            $data,
            '019ff594-0ce1-71f6-a621-0d4b6139a4d7',
            $previousHash,
        );

        $this->assertSame([
            'uuid' => '019ff594-0ce1-71f6-a621-0d4b6139a4d7',
            'previous_hash' => $previousHash,
            'subject_type' => $subject->getMorphClass(),
            'subject_id' => '101',
            'actor_type' => $actor->getMorphClass(),
            'actor_id' => '202',
            'event' => 'updated',
            'old_values' => ['status' => 'pending'],
            'new_values' => ['status' => 'approved'],
            'metadata' => ['source' => 'test'],
            'ip_address' => '203.0.113.10',
            'user_agent' => 'PHPUnit',
            'request_id' => 'request-123',
            'created_at' => '2026-08-11T08:00:00.123456Z',
        ], $payload);
    }

    public function test_it_builds_null_actor_fields_when_actor_is_missing(): void
    {
        $data = new AuditEntryData(
            subject: $this->persistedModel(101),
            event: ModelEvent::Created,
        );

        $payload = (new DefaultAuditPayloadBuilder)->build(
            $data,
            '019ff594-0ce1-71f6-a621-0d4b6139a4d7',
            null,
        );

        $this->assertNull($payload['actor_type']);
        $this->assertNull($payload['actor_id']);
        $this->assertNull($payload['previous_hash']);
    }

    private function persistedModel(int $id): TestModel
    {
        $model = new TestModel;
        $model->setAttribute($model->getKeyName(), $id);
        $model->exists = true;

        return $model;
    }
}
