<?php

namespace Johannesclimacus\ModelAudit\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Johannesclimacus\ModelAudit\Contracts\AuditChainVerifier;
use Johannesclimacus\ModelAudit\DTO\AuditChainVerificationResult;
use Johannesclimacus\ModelAudit\Enums\AuditChainFailure;
use Johannesclimacus\ModelAudit\Models\AuditChainState;
use Johannesclimacus\ModelAudit\Models\AuditEntry;
use Johannesclimacus\ModelAudit\Tests\Support\TestModel;
use Johannesclimacus\ModelAudit\Tests\TestCase;

class AuditChainVerifierTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_verifies_an_unchanged_subject_chain(): void
    {
        $model = TestModel::query()->create([
            'name' => 'Test invoice',
            'status' => 'pending',
        ]);

        $model->status = 'approved';
        $model->save();

        $result = $this->app->make(AuditChainVerifier::class)->verify(
            $model->getMorphClass(),
            (string) $model->getKey(),
        );

        $this->assertTrue($result->valid);
        $this->assertNull($result->failure);
        $this->assertNull($result->failedEntryUuid);
    }

    public function test_it_detects_changed_audit_values(): void
    {
        $model = TestModel::query()->create([
            'name' => 'Test invoice',
            'status' => 'pending',
        ]);

        $model->status = 'approved';
        $model->save();

        $entry = AuditEntry::query()
            ->where('event', 'updated')
            ->sole();

        AuditEntry::query()
            ->whereKey($entry->getKey())
            ->toBase()
            ->update([
                'new_values' => json_encode([
                    'status' => 'tampered',
                ], JSON_THROW_ON_ERROR),
            ]);

        $result = $this->app->make(AuditChainVerifier::class)->verify(
            $model->getMorphClass(),
            (string) $model->getKey(),
        );

        $this->assertFalse($result->valid);
        $this->assertSame(AuditChainFailure::HashMismatch, $result->failure);
        $this->assertSame($entry->uuid, $result->failedEntryUuid);
    }

    public function test_it_accepts_a_subject_without_an_audit_chain(): void
    {
        $result = $this->app->make(AuditChainVerifier::class)->verify(
            TestModel::class,
            'missing-subject',
        );

        $this->assertTrue($result->valid);
    }

    public function test_it_keeps_independent_chains_for_different_subjects(): void
    {
        $first = $this->createSubject('First invoice');
        $second = $this->createSubject('Second invoice');

        $first->status = 'approved';
        $first->save();

        $firstEntries = AuditEntry::query()
            ->where('subject_type', $first->getMorphClass())
            ->where('subject_id', (string) $first->getKey())
            ->orderBy('id')
            ->get();

        $secondEntry = AuditEntry::query()
            ->where('subject_type', $second->getMorphClass())
            ->where('subject_id', (string) $second->getKey())
            ->sole();

        $this->assertCount(2, $firstEntries);
        $this->assertNull($firstEntries[0]->previous_hash);
        $this->assertSame($firstEntries[0]->hash, $firstEntries[1]->previous_hash);
        $this->assertNull($secondEntry->previous_hash);
        $this->assertTrue($this->verify($first)->valid);
        $this->assertTrue($this->verify($second)->valid);
        $this->assertDatabaseCount('audit_chain_states', 2);
    }

    public function test_it_detects_a_deleted_entry_in_the_middle_of_a_chain(): void
    {
        $model = $this->createSubject();

        $model->status = 'approved';
        $model->save();
        $model->status = 'paid';
        $model->save();

        $entries = AuditEntry::query()->orderBy('id')->get();
        $middle = $entries[1];
        $last = $entries[2];

        AuditEntry::query()
            ->whereKey($middle->getKey())
            ->toBase()
            ->delete();

        $result = $this->verify($model);

        $this->assertFalse($result->valid);
        $this->assertSame(AuditChainFailure::PreviousHashMismatch, $result->failure);
        $this->assertSame($last->uuid, $result->failedEntryUuid);
    }

    public function test_it_detects_a_deleted_last_entry(): void
    {
        $model = $this->createSubject();

        $model->status = 'approved';
        $model->save();

        $last = AuditEntry::query()->orderByDesc('id')->firstOrFail();

        AuditEntry::query()
            ->whereKey($last->getKey())
            ->toBase()
            ->delete();

        $result = $this->verify($model);

        $this->assertFalse($result->valid);
        $this->assertSame(AuditChainFailure::EntryCountMismatch, $result->failure);
    }

    public function test_it_detects_a_changed_previous_hash(): void
    {
        $model = $this->createSubject();

        $model->status = 'approved';
        $model->save();

        $entry = AuditEntry::query()->orderByDesc('id')->firstOrFail();

        AuditEntry::query()
            ->whereKey($entry->getKey())
            ->toBase()
            ->update(['previous_hash' => str_repeat('f', 64)]);

        $result = $this->verify($model);

        $this->assertFalse($result->valid);
        $this->assertSame(AuditChainFailure::PreviousHashMismatch, $result->failure);
        $this->assertSame($entry->uuid, $result->failedEntryUuid);
    }

    public function test_it_detects_a_changed_entry_count(): void
    {
        $model = $this->createSubject();
        $state = AuditChainState::query()->sole();

        AuditChainState::query()
            ->whereKey($state->getKey())
            ->toBase()
            ->update(['entries_count' => 99]);

        $result = $this->verify($model);

        $this->assertFalse($result->valid);
        $this->assertSame(AuditChainFailure::EntryCountMismatch, $result->failure);
    }

    public function test_it_detects_a_changed_last_hash(): void
    {
        $model = $this->createSubject();
        $entry = AuditEntry::query()->sole();
        $state = AuditChainState::query()->sole();

        AuditChainState::query()
            ->whereKey($state->getKey())
            ->toBase()
            ->update(['last_hash' => str_repeat('f', 64)]);

        $result = $this->verify($model);

        $this->assertFalse($result->valid);
        $this->assertSame(AuditChainFailure::LastHashMismatch, $result->failure);
        $this->assertSame($entry->uuid, $result->failedEntryUuid);
    }

    public function test_it_detects_a_changed_last_entry_uuid(): void
    {
        $model = $this->createSubject();
        $entry = AuditEntry::query()->sole();
        $state = AuditChainState::query()->sole();

        AuditChainState::query()
            ->whereKey($state->getKey())
            ->toBase()
            ->update([
                'last_entry_uuid' => '019ff594-0ce1-71f6-a621-0d4b6139a4d8',
            ]);

        $result = $this->verify($model);

        $this->assertFalse($result->valid);
        $this->assertSame(AuditChainFailure::LastEntryUuidMismatch, $result->failure);
        $this->assertSame($entry->uuid, $result->failedEntryUuid);
    }

    public function test_it_detects_a_missing_chain_state(): void
    {
        $model = $this->createSubject();
        $entry = AuditEntry::query()->sole();

        AuditChainState::query()->toBase()->delete();

        $result = $this->verify($model);

        $this->assertFalse($result->valid);
        $this->assertSame(AuditChainFailure::StateMissing, $result->failure);
        $this->assertSame($entry->uuid, $result->failedEntryUuid);
    }

    private function createSubject(string $name = 'Test invoice'): TestModel
    {
        return TestModel::query()->create([
            'name' => $name,
            'status' => 'pending',
        ]);
    }

    private function verify(TestModel $model): AuditChainVerificationResult
    {
        return $this->app->make(AuditChainVerifier::class)->verify(
            $model->getMorphClass(),
            (string) $model->getKey(),
        );
    }
}
