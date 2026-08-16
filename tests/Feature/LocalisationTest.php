<?php

namespace Johannesclimacus\ModelAudit\Tests\Feature;

use Johannesclimacus\ModelAudit\Exceptions\AuditEntryIsImmutable;
use Johannesclimacus\ModelAudit\Tests\TestCase;

class LocalisationTest extends TestCase
{
    public function test_it_loads_english_translations(): void
    {
        app()->setLocale('en');

        $this->assertSame('Created', __('model-audit::events.created'));
    }

    public function test_it_loads_russian_translations(): void
    {
        app()->setLocale('ru');

        $this->assertSame('Создание', __('model-audit::events.created'));
    }

    public function test_it_falls_back_to_the_event_name(): void
    {
        app()->setLocale('en');

        $event = 'invoice.approved';
        $key = 'model-audit::events.' . $event;
        $translation = __($key);

        $this->assertSame($key, $translation);
    }

    public function test_it_translates_exception_to_english(): void
    {
        app()->setLocale('en');

        $exception = AuditEntryIsImmutable::forUpdate();

        $this->assertSame('Audit entries cannot be updated.', $exception->getMessage());
    }

    public function test_it_translates_exception_to_russian(): void
    {
        app()->setLocale('ru');

        $exception = AuditEntryIsImmutable::forUpdate();

        $this->assertSame('Записи аудита нельзя изменять.', $exception->getMessage());
    }
}
