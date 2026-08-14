<?php

namespace Local\ModelAudit\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Local\ModelAudit\Contracts\AuditChainVerifier;
use Local\ModelAudit\Enums\AuditChainFailure;
use Local\ModelAudit\Models\AuditEntry;
use Local\ModelAudit\Tests\Support\TestModel;
use Local\ModelAudit\Tests\TestCase;

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
}
