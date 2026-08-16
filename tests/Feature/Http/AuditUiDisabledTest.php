<?php

namespace Johannesclimacus\ModelAudit\Tests\Feature\Http;

use Illuminate\Support\Facades\Route;
use Johannesclimacus\ModelAudit\Tests\TestCase;

class AuditUiDisabledTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('model-audit.ui.enabled', false);
    }

    public function test_audit_routes_are_not_registered_when_ui_is_disabled(): void
    {
        $this->assertFalse(Route::has('model-audit.index'));
        $this->assertFalse(Route::has('model-audit.show'));
        $this->assertFalse(Route::has('model-audit.subject'));
    }
}
