<?php

namespace Local\ModelAudit\Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Local\ModelAudit\Contracts\AuditLogger;
use Local\ModelAudit\Models\AuditEntry;
use Local\ModelAudit\Tests\Support\TestModel;
use Local\ModelAudit\Tests\Support\TestUser;
use Local\ModelAudit\Tests\TestCase;

class AuditControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('model-audit.ui.middleware', [
            'web',
            'auth',
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        Route::get('/login', fn (): string => 'Login')->name('login');

        Gate::define(
            'viewModelAudit',
            fn (TestUser $user): bool => $user->status === 'active',
        );
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('model-audit.index'))
            ->assertRedirect(route('login'));
    }

    public function test_unauthorized_user_cannot_view_audit_index(): void
    {
        $this->actingAs($this->createUser('blocked'))
            ->get(route('model-audit.index'))
            ->assertForbidden();
    }

    public function test_authorized_user_can_view_audit_index(): void
    {
        $this->actingAs($this->createUser())
            ->get(route('model-audit.index'))
            ->assertOk()
            ->assertSee('Audit journal');
    }

    public function test_audit_index_displays_entries_and_links(): void
    {
        $this->actingAs($this->createUser());
        $model = $this->createSubject();
        $entry = AuditEntry::query()->sole();

        $response = $this->get(route('model-audit.index'));

        $response
            ->assertOk()
            ->assertSee($entry->uuid)
            ->assertSee('Created')
            ->assertSee(route('model-audit.show', $entry->uuid), false)
            ->assertSee(route('model-audit.subject', [
                'type' => $model->getMorphClass(),
                'id' => (string) $model->getKey(),
            ]), false);
    }

    public function test_audit_index_displays_an_empty_state(): void
    {
        $this->actingAs($this->createUser())
            ->get(route('model-audit.index'))
            ->assertOk()
            ->assertSee('No audit entries found');
    }

    public function test_audit_index_filters_entries(): void
    {
        $this->actingAs($this->createUser());

        $model = $this->createSubject('First invoice');
        $created = AuditEntry::query()->sole();

        $this->createSubject('Second invoice');

        $model->update(['status' => 'approved']);
        $updated = AuditEntry::query()->where('event', 'updated')->sole();

        $response = $this->get(route('model-audit.index', [
            'event' => 'updated',
            'subject_type' => $model->getMorphClass(),
            'subject_id' => (string) $model->getKey(),
        ]));

        $response
            ->assertOk()
            ->assertSee($updated->uuid)
            ->assertDontSee($created->uuid);
    }

    public function test_audit_index_paginates_entries(): void
    {
        config()->set('model-audit.ui.per_page', 1);
        $this->actingAs($this->createUser());

        $this->createSubject('First invoice');
        $first = AuditEntry::query()->sole();
        $this->createSubject('Second invoice');
        $second = AuditEntry::query()->orderByDesc('id')->firstOrFail();

        $this->get(route('model-audit.index'))
            ->assertOk()
            ->assertSee($second->uuid)
            ->assertDontSee($first->uuid)
            ->assertSee(route('model-audit.index', ['page' => 2]), false);

        $this->get(route('model-audit.index', ['page' => 2]))
            ->assertOk()
            ->assertSee($first->uuid)
            ->assertDontSee($second->uuid);
    }

    public function test_it_displays_an_audit_entry(): void
    {
        $this->actingAs($this->createUser());
        $model = $this->createSubject();
        $model->update(['status' => 'approved']);
        $entry = AuditEntry::query()->where('event', 'updated')->sole();

        $this->get(route('model-audit.show', $entry->uuid))
            ->assertOk()
            ->assertSee($entry->uuid)
            ->assertSee('pending')
            ->assertSee('approved')
            ->assertSee($entry->hash);
    }

    public function test_it_displays_subject_history_and_integrity(): void
    {
        $this->actingAs($this->createUser());
        $model = $this->createSubject();
        $model->update(['status' => 'approved']);
        $entries = AuditEntry::query()->orderBy('id')->get();

        $response = $this->get(route('model-audit.subject', [
            'type' => $model->getMorphClass(),
            'id' => (string) $model->getKey(),
        ]));

        $response
            ->assertOk()
            ->assertSee('Integrity verified')
            ->assertSee($entries[0]->uuid)
            ->assertSee($entries[1]->uuid);
    }

    public function test_it_displays_a_failed_subject_chain(): void
    {
        $this->actingAs($this->createUser());
        $model = $this->createSubject();
        $entry = AuditEntry::query()->sole();

        AuditEntry::query()
            ->whereKey($entry->getKey())
            ->toBase()
            ->update(['hash' => str_repeat('f', 64)]);

        $this->get(route('model-audit.subject', [
            'type' => $model->getMorphClass(),
            'id' => (string) $model->getKey(),
        ]))
            ->assertOk()
            ->assertSee('Integrity verification failed')
            ->assertSee('Entry hash does not match.');
    }

    public function test_it_displays_a_custom_event_without_a_translation(): void
    {
        $this->actingAs($this->createUser());
        $model = $this->createSubject();

        $this->app->make(AuditLogger::class)->record(
            subject: $model,
            event: 'invoice.approved',
        );

        $this->get(route('model-audit.index', ['event' => 'invoice.approved']))
            ->assertOk()
            ->assertSee('invoice.approved')
            ->assertDontSee('model-audit::events.invoice.approved');
    }

    public function test_it_displays_the_ui_in_russian(): void
    {
        app()->setLocale('ru');

        $this->actingAs($this->createUser())
            ->get(route('model-audit.index'))
            ->assertOk()
            ->assertSee('Журнал аудита')
            ->assertSee('Применить');
    }

    private function createUser(string $status = 'active'): TestUser
    {
        return TestUser::query()->create([
            'name' => 'Audit viewer',
            'status' => $status,
        ]);
    }

    private function createSubject(string $name = 'Test invoice'): TestModel
    {
        return TestModel::query()->create([
            'name' => $name,
            'status' => 'pending',
        ]);
    }
}
