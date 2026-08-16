<?php

namespace Local\ModelAudit\Tests\Feature\Http\Middleware;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Local\ModelAudit\Http\Middleware\AuthorizeModelAudit;
use Local\ModelAudit\Tests\Support\TestUser;
use Local\ModelAudit\Tests\TestCase;

class AuthorizeModelAuditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(AuthorizeModelAudit::class)
            ->get('/model-audit-test', function (): string {
                return 'Audit page';
            });
    }

    public function test_it_allows_an_authorized_user(): void
    {
        $user = TestUser::create([
            'name' => 'Allowed User',
            'status' => 'active'
        ]);

        Gate::define('viewModelAudit', function (TestUser $authenticatedUser): bool
        {
            return $authenticatedUser->status === 'active';
        });

        $response = $this->actingAs($user)
            ->get('/model-audit-test');

        $response->assertOk();
        $response->assertSee('Audit page');
    }

    public function test_it_rejects_an_unauthorized_user(): void
    {
        $user = TestUser::create([
           'name' => 'Test User',
           'status' => 'banned lol'
        ]);

        Gate::define('viewModelAudit', function (TestUser $authenticatedUser): bool{
            return $authenticatedUser->status === 'active';
        });

        $response = $this->actingAs($user)
            ->get('/model-audit-test');

        $response->assertForbidden();
    }
}
