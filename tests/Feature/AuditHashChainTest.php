<?php

namespace Johannesclimacus\ModelAudit\Tests\Feature;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Johannesclimacus\ModelAudit\Contracts\AuditRecorder;
use Johannesclimacus\ModelAudit\DTO\AuditEntryData;
use Johannesclimacus\ModelAudit\Enums\ModelEvent;
use Johannesclimacus\ModelAudit\Models\AuditChainState;
use Johannesclimacus\ModelAudit\Models\AuditEntry;
use Johannesclimacus\ModelAudit\Tests\Support\TestModel;
use Johannesclimacus\ModelAudit\Tests\TestCase;

class AuditHashChainTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_the_first_entry_in_a_subject_chain(): void
    {
        $subject = $this->persistedModel(101);

        $entry = $this->app->make(AuditRecorder::class)->record(
            new AuditEntryData(
                subject: $subject,
                event: ModelEvent::Created,
                newValues: ['status' => 'pending'],
                createdAt: CarbonImmutable::parse('2026-08-11 08:00:00.123456', 'UTC'),
            ),
        );

        $state = AuditChainState::query()->sole();

        $this->assertNull($entry->previous_hash);
        $this->assertMatchesRegularExpression('/\A[a-f0-9]{64}\z/', $entry->hash);
        $this->assertSame($entry->hash, $state->last_hash);
        $this->assertSame($entry->uuid, $state->last_entry_uuid);
        $this->assertSame(1, $state->entries_count);
    }

    public function test_it_links_a_new_entry_to_the_previous_entry(): void
    {
        $subject = $this->persistedModel(101);
        $recorder = $this->app->make(AuditRecorder::class);

        $first = $recorder->record(new AuditEntryData(
            subject: $subject,
            event: ModelEvent::Created,
            newValues: ['status' => 'pending'],
        ));

        $second = $recorder->record(new AuditEntryData(
            subject: $subject,
            event: ModelEvent::Updated,
            oldValues: ['status' => 'pending'],
            newValues: ['status' => 'approved'],
        ));

        $state = AuditChainState::query()->sole();

        $this->assertSame($first->hash, $second->previous_hash);
        $this->assertNotSame($first->hash, $second->hash);
        $this->assertSame($second->hash, $state->last_hash);
        $this->assertSame($second->uuid, $state->last_entry_uuid);
        $this->assertSame(2, $state->entries_count);
        $this->assertSame(2, AuditEntry::query()->count());
    }

    private function persistedModel(int $id): TestModel
    {
        $model = new TestModel;
        $model->setAttribute($model->getKeyName(), $id);
        $model->exists = true;

        return $model;
    }
}
