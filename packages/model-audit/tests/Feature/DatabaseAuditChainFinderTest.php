<?php

namespace Johannesclimacus\ModelAudit\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Johannesclimacus\ModelAudit\Contracts\AuditChainFinder;
use Johannesclimacus\ModelAudit\Models\AuditChainState;
use Johannesclimacus\ModelAudit\Models\AuditEntry;
use Johannesclimacus\ModelAudit\Tests\Support\TestModel;
use Johannesclimacus\ModelAudit\Tests\TestCase;

class DatabaseAuditChainFinderTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_finds_an_audit_chain(): void
    {
        $model = TestModel::query()->create([
            'name' => 'Test invoice',
            'status' => 'pending',
        ]);

        $finder = $this->app->make(AuditChainFinder::class);

        $identifiers = iterator_to_array($finder->all());

        $this->assertCount(1, $identifiers);

        $this->assertSame($model->getMorphClass(), $identifiers[0]->subjectType);

        $this->assertSame((string) $model->getKey(), $identifiers[0]->subjectId);
    }

    public function test_it_finds_a_chain_when_its_state_is_missing(): void
    {
        $model = TestModel::query()->create([
            'name' => 'Test invoice',
            'status' => 'pending',
        ]);

        AuditChainState::query()
            ->where('subject_type', $model->getMorphClass())
            ->where('subject_id', (string) $model->getKey())
            ->delete();

        $identifiers = iterator_to_array(
            $this->app->make(AuditChainFinder::class)->all()
        );

        $this->assertCount(1, $identifiers);
        $this->assertSame($model->getMorphClass(), $identifiers[0]->subjectType);
        $this->assertSame((string) $model->getKey(), $identifiers[0]->subjectId);
    }

    public function test_it_finds_a_chain_when_its_entries_are_missing(): void
    {
        $model = TestModel::query()->create([
            'name' => 'Test invoice',
            'status' => 'pending',
        ]);

        AuditEntry::query()
            ->where('subject_type', $model->getMorphClass())
            ->where('subject_id', (string) $model->getKey())
            ->toBase()
            ->delete();

        $identifiers = iterator_to_array(
            $this->app->make(AuditChainFinder::class)->all()
        );

        $this->assertCount(1, $identifiers);
        $this->assertSame($model->getMorphClass(), $identifiers[0]->subjectType);
        $this->assertSame((string) $model->getKey(), $identifiers[0]->subjectId);
    }
}
