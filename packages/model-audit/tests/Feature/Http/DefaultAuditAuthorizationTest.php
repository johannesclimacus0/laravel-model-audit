<?php

namespace Local\ModelAudit\Tests\Feature\Http;

use Local\ModelAudit\Tests\TestCase;

class DefaultAuditAuthorizationTest extends TestCase
{
    public function test_guest_is_forbidden_without_a_login_route(): void
    {
        $this->get(route('model-audit.index'))
            ->assertForbidden();
    }
}
