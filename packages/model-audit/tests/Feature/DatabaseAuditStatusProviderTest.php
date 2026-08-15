<?php

namespace Local\ModelAudit\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Local\ModelAudit\Models\AuditEntry;
use Local\ModelAudit\Status\DatabaseAuditStatusProvider;
use Local\ModelAudit\Tests\Support\TestModel;
use Local\ModelAudit\Tests\TestCase;

class DatabaseAuditStatusProviderTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_status_for_an_empty_audit_database(): void
    {
        config()->set('model-audit.enabled', true);

        $provider = new DatabaseAuditStatusProvider;

        $status = $provider->get();

        $this->assertTrue($status->enabled);
        $this->assertSame('testing', $status->connectionName);
        $this->assertSame('audit_entries', $status->entriesTable);
        $this->assertSame('audit_chain_states', $status->chainStatesTable);
        $this->assertSame(0, $status->entriesCount);
        $this->assertSame(0, $status->chainsCount);
        $this->assertNull($status->lastEntryAt);
    }

    public function test_it_returns_counts_and_last_entry_time(): void
    {
        $model = TestModel::query()->create([
            'name' => 'Test invoice',
            'status' => 'pending',
        ]);

        $model->update(['status' => 'approved']);

        $lastEntry = AuditEntry::query()
            ->latest('id')
            ->firstOrFail();

        $status = new DatabaseAuditStatusProvider()->get();

        $this->assertSame(2, $status->entriesCount);
        $this->assertSame(1, $status->chainsCount);
        $this->assertNotNull($status->lastEntryAt);
        $this->assertTrue($lastEntry->created_at->equalTo($status->lastEntryAt));
    }
}
