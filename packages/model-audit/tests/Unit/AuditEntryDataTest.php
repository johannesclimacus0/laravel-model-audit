<?php

namespace Local\ModelAudit\Tests\Unit;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Local\ModelAudit\DTO\AuditEntryData;
use Local\ModelAudit\Enums\ModelEvent;
use Local\ModelAudit\Tests\TestCase;

class AuditEntryDataTest extends TestCase
{
    public function test_it_normalizes_a_model_event_enum(): void
    {
        $data = new AuditEntryData(subject: $this->subject(), event: ModelEvent::Updated);

        $this->assertSame('updated', $data->event);
    }

    public function test_it_accepts_a_custom_event(): void
    {
        $data = new AuditEntryData(subject: $this->subject(), event: ' invoice.approved ');

        $this->assertSame('invoice.approved', $data->event);
    }

    public function test_it_rejects_an_empty_event(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Audit event cannot be empty.');

        new AuditEntryData(subject: $this->subject(), event: '   ');
    }

    public function test_it_normalizes_time_to_utc(): void
    {
        $sydneyTime = CarbonImmutable::parse('2026-08-11 18:30:00.123456', 'Australia/Sydney');

        $data = new AuditEntryData(subject: $this->subject(), event: ModelEvent::Created, createdAt: $sydneyTime);

        $this->assertSame('UTC', $data->createdAt->timezoneName);

        $this->assertSame('2026-08-11 08:30:00.123456', $data->createdAt->format('Y-m-d H:i:s.u'));
    }

    private function subject(): Model
    {
        return new class extends Model {};
    }
}
