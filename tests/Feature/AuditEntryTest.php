<?php

namespace Johannesclimacus\ModelAudit\Tests\Feature;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Johannesclimacus\ModelAudit\Exceptions\AuditEntryIsImmutable;
use Johannesclimacus\ModelAudit\Models\AuditEntry;
use Johannesclimacus\ModelAudit\Tests\TestCase;

class AuditEntryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_an_audit_entry(): void
    {
        $entry = $this->createEntry();

        $this->assertNotEmpty($entry->uuid);
        $this->assertSame(['status' => 'approved'], $entry->new_values);
        $this->assertSame(['source' => 'test'], $entry->metadata);
        $this->assertInstanceOf(CarbonImmutable::class, $entry->created_at);
        $this->assertDatabaseHas('audit_entries', [
            'uuid' => $entry->uuid,
            'event' => 'created',
            'subject_type' => 'some-subject',
            'subject_id' => 'test-subject-id-1',
        ]);
    }

    public function test_it_cant_be_updated(): void
    {
        $entry = $this->createEntry();

        try {
            $entry->update(['event' => 'rejected']);

            $this->fail();
        } catch (AuditEntryIsImmutable) {
        }

        $this->assertSame('created', $entry->fresh()->event);
    }

    public function test_it_cannot_be_deleted(): void
    {
        $entry = $this->createEntry();

        try {
            $entry->delete();

            $this->fail();
        } catch (AuditEntryIsImmutable) {
        }

        $this->assertDatabaseHas('audit_entries', ['id' => $entry->id]);
    }

    private function createEntry(): AuditEntry
    {
        return AuditEntry::query()->create([
            'subject_type' => 'some-subject',
            'subject_id' => 'test-subject-id-1',
            'event' => 'created',
            'old_values' => null,
            'new_values' => [
                'status' => 'approved',
            ],
            'metadata' => [
                'source' => 'test',
            ],
            'created_at' => CarbonImmutable::parse('2026-08-11 12:34:56.789101', 'UTC'),
        ]);
    }
}
