<?php

namespace Johannesclimacus\ModelAudit\Tests\Feature\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Johannesclimacus\ModelAudit\Models\AuditEntry;
use Johannesclimacus\ModelAudit\Tests\Support\TestModel;
use Johannesclimacus\ModelAudit\Tests\TestCase;

class VerifyAllAuditChainsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_success_when_there_are_no_chains(): void
    {
        app()->setLocale('en');

        $this->artisan('model-audit:verify-all')
            ->expectsOutput('No audit chains found.')
            ->assertSuccessful();
    }

    public function test_it_returns_success_when_all_chains_are_valid(): void
    {
        app()->setLocale('en');

        TestModel::query()->create([
            'name' => 'First invoice',
            'status' => 'pending',
        ]);

        TestModel::query()->create([
            'name' => 'Second invoice',
            'status' => 'approved',
        ]);

        $this->artisan('model-audit:verify-all')
            ->expectsOutput('Verified chains: 2')
            ->expectsOutput('All audit chains are valid.')
            ->assertSuccessful();
    }

    public function test_it_returns_failure_and_reports_invalid_chains(): void
    {
        app()->setLocale('en');

        $model = TestModel::query()->create([
            'name' => 'First invoice',
            'status' => 'pending',
        ]);

        TestModel::query()->create([
            'name' => 'Second invoice',
            'status' => 'approved',
        ]);

        $entry = AuditEntry::query()
            ->where('subject_type', $model->getMorphClass())
            ->where('subject_id', (string) $model->getKey())
            ->sole();

        AuditEntry::query()
            ->whereKey($entry->getKey())
            ->toBase()
            ->update([
                'event' => 'tampered',
            ]);

        $this->artisan('model-audit:verify-all')
            ->expectsOutput(
                'Invalid audit chain: ' . $model->getMorphClass() . ' [' . $model->getKey() . ']'
            )
            ->expectsOutput('Reason: hash_mismatch')
            ->expectsOutput('Entry UUID: ' . $entry->uuid)
            ->expectsOutput('Verified chains: 2')
            ->expectsOutput('Invalid chains: 1')
            ->assertExitCode(Command::FAILURE);
    }

    public function test_it_uses_russian_translations(): void
    {
        app()->setLocale('ru');

        $this->artisan('model-audit:verify-all')
            ->expectsOutput('Цепочки аудита не найдены.')
            ->assertSuccessful();
    }
}
