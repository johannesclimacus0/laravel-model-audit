<?php

namespace Johannesclimacus\ModelAudit\Tests\Feature;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Johannesclimacus\ModelAudit\Models\AuditChainState;
use Johannesclimacus\ModelAudit\Tests\TestCase;

class AuditChainStateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_chain_state_for_a_subject(): void
    {
        $state = AuditChainState::query()->create([
            'subject_type' => 'test-subject',
            'subject_id' => 'subject-1',
            'last_hash' => str_repeat('a', 64),
            'last_entry_uuid' => '019ff594-0ce1-71f6-a621-0d4b6139a4d7',
            'entries_count' => 1,
        ]);

        $this->assertSame('test-subject', $state->subject_type);
        $this->assertSame('subject-1', $state->subject_id);
        $this->assertSame(1, $state->entries_count);
        $this->assertInstanceOf(CarbonImmutable::class, $state->created_at);
        $this->assertInstanceOf(CarbonImmutable::class, $state->updated_at);

        $this->assertDatabaseHas('audit_chain_states', [
            'subject_type' => 'test-subject',
            'subject_id' => 'subject-1',
            'last_hash' => str_repeat('a', 64),
            'entries_count' => 1,
        ]);
    }

    public function test_it_updates_the_end_of_a_subject_chain(): void
    {
        $state = AuditChainState::query()->create([
            'subject_type' => 'test-subject',
            'subject_id' => 'subject-1',
            'entries_count' => 0,
        ]);

        $state->update([
            'last_hash' => str_repeat('b', 64),
            'last_entry_uuid' => '019ff594-0ce1-71f6-a621-0d4b6139a4d7',
            'entries_count' => 1,
        ]);

        $state->refresh();

        $this->assertSame(str_repeat('b', 64), $state->last_hash);
        $this->assertSame('019ff594-0ce1-71f6-a621-0d4b6139a4d7', $state->last_entry_uuid);
        $this->assertSame(1, $state->entries_count);
    }
}
