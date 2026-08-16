<?php

namespace Johannesclimacus\ModelAudit\Tests\Feature\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Johannesclimacus\ModelAudit\Models\AuditEntry;
use Johannesclimacus\ModelAudit\Tests\Support\TestModel;
use Johannesclimacus\ModelAudit\Tests\TestCase;

class AuditStatusCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_displays_status_for_an_empty_audit_database(): void
    {
        app()->setLocale('en');

        config()->set('model-audit.enabled', true);

        $output = $this->runStatusCommand();

        foreach ([
            'Audit status',
            'Enabled',
            'Yes',
            'testing',
            'audit_entries',
            'audit_chain_states',
            'Audit entries',
            'Chains',
            'never',
        ] as $expected) {
            $this->assertStringContainsString($expected, $output);
        }
    }

    public function test_it_displays_disabled_status(): void
    {
        app()->setLocale('en');

        config()->set('model-audit.enabled', false);

        $output = $this->runStatusCommand();

        $this->assertStringContainsString('No', $output);
    }

    public function test_it_displays_counts_and_last_entry_time(): void
    {
        app()->setLocale('en');

        $model = TestModel::query()->create([
            'name' => 'Test invoice',
            'status' => 'pending',
        ]);

        $model->update([
            'status' => 'approved',
        ]);

        $lastEntry = AuditEntry::query()
            ->latest('id')
            ->firstOrFail();

        $output = $this->runStatusCommand();

        $this->assertStringContainsString('Audit entries', $output);
        $this->assertStringContainsString('2', $output);
        $this->assertStringContainsString('Chains', $output);
        $this->assertStringContainsString('1', $output);
        $this->assertStringContainsString(
            $lastEntry->created_at->toIso8601String(),
            $output,
        );
    }

    public function test_it_displays_status_in_russian(): void
    {
        app()->setLocale('ru');

        config()->set('model-audit.enabled', true);

        $output = $this->runStatusCommand();

        foreach ([
            'Статус аудита',
            'Включен',
            'Да',
            'Подключение',
            'Таблица записей',
            'Таблица состояний цепочек',
            'Записей аудита',
            'Цепочек',
            'Последняя запись',
            'отсутствует',
        ] as $expected) {
            $this->assertStringContainsString($expected, $output);
        }
    }

    private function runStatusCommand(): string
    {
        $exitCode = Artisan::call('model-audit:status', [
            '--no-ansi' => true,
        ]);

        $this->assertSame(Command::SUCCESS, $exitCode);

        return Artisan::output();
    }
}
