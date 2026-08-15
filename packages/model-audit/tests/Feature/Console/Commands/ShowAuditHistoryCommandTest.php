<?php

namespace Local\ModelAudit\Tests\Feature\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use JsonException;
use Local\ModelAudit\Models\AuditEntry;
use Local\ModelAudit\Tests\Support\TestModel;
use Local\ModelAudit\Tests\TestCase;

class ShowAuditHistoryCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_displays_history_for_a_subject(): void
    {
        app()->setLocale('en');

        $model = $this->createModel();
        $entry = AuditEntry::query()->sole();

        $exitCode = Artisan::call('model-audit:show', [
            'subjectType' => $model->getMorphClass(),
            'subjectId' => (string) $model->getKey(),
            '--no-ansi' => true,
        ]);

        $output = Artisan::output();

        $this->assertSame(Command::SUCCESS, $exitCode);
        $this->assertStringContainsString('Audit history', $output);
        $this->assertStringContainsString($entry->uuid, $output);
        $this->assertStringContainsString('created', $output);
        $this->assertStringContainsString('system', $output);
    }

    public function test_it_filters_history_by_event(): void
    {
        $model = $this->createModel();

        $model->update(['status' => 'approved']);

        $createdEntry = AuditEntry::query()
            ->where('event', 'created')
            ->sole();

        $updatedEntry = AuditEntry::query()
            ->where('event', 'updated')
            ->sole();

        Artisan::call('model-audit:show', [
            'subjectType' => $model->getMorphClass(),
            'subjectId' => (string) $model->getKey(),
            '--event' => 'created',
            '--no-ansi' => true,
        ]);

        $output = Artisan::output();

        $this->assertStringContainsString($createdEntry->uuid, $output);
        $this->assertStringNotContainsString($updatedEntry->uuid, $output);
    }

    public function test_it_limits_history_results(): void
    {
        $model = $this->createModel();

        foreach (['approved', 'paid'] as $status) {
            $model->update([
                'status' => $status,
            ]);
        }

        $entries = AuditEntry::query()
            ->orderByDesc('id')
            ->get();

        Artisan::call('model-audit:show', [
            'subjectType' => $model->getMorphClass(),
            'subjectId' => (string) $model->getKey(),
            '--limit' => 1,
            '--no-ansi' => true,
        ]);

        $output = Artisan::output();

        $this->assertStringContainsString($entries[0]->uuid, $output);
        $this->assertStringNotContainsString($entries[1]->uuid, $output);
    }

    public function test_it_outputs_history_as_json(): void
    {
        $model = $this->createModel();
        $entry = AuditEntry::query()->sole();

        $exitCode = Artisan::call('model-audit:show', [
            'subjectType' => $model->getMorphClass(),
            'subjectId' => (string) $model->getKey(),
            '--json' => true,
            '--no-ansi' => true,
        ]);

        $data = json_decode(Artisan::output(), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame(Command::SUCCESS, $exitCode);
        $this->assertCount(1, $data);
        $this->assertSame($entry->uuid, $data[0]['uuid']);
        $this->assertSame('created', $data[0]['event']);
        $this->assertSame($entry->new_values, $data[0]['new_values']);
    }

    public function test_it_reports_when_history_is_empty(): void
    {
        app()->setLocale('en');

        $exitCode = Artisan::call('model-audit:show', [
            'subjectType' => TestModel::class,
            'subjectId' => 'missing',
            '--no-ansi' => true,
        ]);

        $this->assertSame(Command::SUCCESS, $exitCode);
        $this->assertStringContainsString(
            'No audit entries found.',
            Artisan::output(),
        );
    }

    public function test_it_rejects_an_invalid_limit(): void
    {
        app()->setLocale('en');

        $exitCode = Artisan::call('model-audit:show', [
            'subjectType' => TestModel::class,
            'subjectId' => '1',
            '--limit' => 'invalid',
            '--no-ansi' => true,
        ]);

        $this->assertSame(Command::INVALID, $exitCode);
        $this->assertStringContainsString(
            'Limit must be an integer.',
            Artisan::output(),
        );
    }

    public function test_it_displays_empty_history_in_russian(): void
    {
        app()->setLocale('ru');

        $exitCode = Artisan::call('model-audit:show', [
            'subjectType' => TestModel::class,
            'subjectId' => 'missing',
            '--no-ansi' => true,
        ]);

        $this->assertSame(Command::SUCCESS, $exitCode);
        $this->assertStringContainsString(
            'Записи аудита не найдены.',
            Artisan::output(),
        );
    }

    private function createModel(): TestModel
    {
        return TestModel::query()->create([
            'name' => 'Test invoice',
            'status' => 'pending',
        ]);
    }
}
