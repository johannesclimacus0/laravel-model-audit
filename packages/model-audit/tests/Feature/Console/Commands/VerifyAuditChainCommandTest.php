<?php

namespace Local\ModelAudit\Tests\Feature\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Local\ModelAudit\Models\AuditEntry;
use Local\ModelAudit\Tests\Support\TestModel;
use Local\ModelAudit\Tests\TestCase;

class VerifyAuditChainCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_success_for_a_valid_chain(): void
    {
        app()->setLocale('en');

        $model = TestModel::query()->create([
            'name' => 'Test invoice',
            'status' => 'pending',
        ]);

        $this->artisan('model-audit:verify', [
            'subjectType' => $model->getMorphClass(),
            'subjectId' => (string) $model->getKey(),
        ])
            ->expectsOutput('Audit chain is valid.')
            ->assertSuccessful();
    }

    public function test_it_returns_failure_for_an_invalid_chain(): void
    {
        app()->setLocale('en');

        $model = TestModel::query()->create([
            'name' => 'Test invoice',
            'status' => 'pending',
        ]);

        $entry = AuditEntry::query()->sole();

        AuditEntry::query()
            ->whereKey($entry->getKey())
            ->toBase()
            ->update([
                'event' => 'tampered',
            ]);

        $command = $this->artisan('model-audit:verify', [
            'subjectType' => $model->getMorphClass(),
            'subjectId' => (string) $model->getKey(),
        ]);
        $command->expectsOutput('Audit chain is invalid.');
        $command->expectsOutput('Reason: hash_mismatch');
        $command->expectsOutput('Entry UUID: ' . $entry->uuid);
        $command->assertExitCode(Command::FAILURE);
    }
}
