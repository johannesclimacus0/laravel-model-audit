<?php

namespace Local\ModelAudit\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Local\ModelAudit\Models\AuditEntry;
use Local\ModelAudit\Tests\Support\ExcludedFieldsModel;
use Local\ModelAudit\Tests\Support\MaskedModel;
use Local\ModelAudit\Tests\Support\SoftDeletedModel;
use Local\ModelAudit\Tests\Support\TestModel;
use Local\ModelAudit\Tests\Support\TestUser;
use Local\ModelAudit\Tests\TestCase;

class AuditableObserverTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_records_a_created_model(): void
    {
        $model = TestModel::query()->create([
            'name' => 'Test invoice',
            'status' => 'pending',
        ]);

        $entry = AuditEntry::query()->sole();

        $this->assertSame('created', $entry->event);
        $this->assertSame($model->getMorphClass(), $entry->subject_type);
        $this->assertSame((string) $model->getKey(), $entry->subject_id);
        $this->assertSame('Test invoice', $entry->new_values['name']);
        $this->assertSame('pending', $entry->new_values['status']);
        $this->assertTrue($entry->subject->is($model));
        $this->assertTrue($model->audits->contains($entry));
    }

    public function test_it_records_only_changed_values(): void
    {
        $model = TestModel::query()->create([
            'name' => 'Test invoice',
            'status' => 'pending',
        ]);

        $model->status = 'approved';
        $model->save();

        $entry = AuditEntry::query()
            ->where('event', 'updated')
            ->sole();

        $this->assertSame(['status' => 'pending'], $entry->old_values);
        $this->assertSame(['status' => 'approved'], $entry->new_values);
    }

    public function test_it_records_a_deleted_model(): void
    {
        $model = TestModel::query()->create([
            'name' => 'Test invoice',
            'status' => 'pending',
        ]);

        $id = $model->getKey();

        $model->delete();

        $entry = AuditEntry::query()
            ->where('event', 'deleted')
            ->sole();

        $this->assertSame((string) $id, $entry->subject_id);
        $this->assertSame('pending', $entry->old_values['status']);
        $this->assertSame('Test invoice', $entry->old_values['name']);
        $this->assertNull($entry->new_values);

        $this->assertDatabaseMissing('test_models', [
            'id' => $id,
        ]);
    }

    public function test_it_excludes_model_configured_fields(): void
    {
        ExcludedFieldsModel::query()->create([
            'name' => 'Test invoice',
            'status' => 'pending',
        ]);

        $entry = AuditEntry::query()
            ->where('event', 'created')
            ->sole();

        $this->assertSame(['status' => 'pending'], $entry->new_values);
    }

    public function test_it_does_not_record_an_update_when_all_of_the_changed_fields_are_excluded(): void
    {
        $model = ExcludedFieldsModel::query()->create([
            'name' => 'Test invoice',
            'status' => 'pending',
        ]);

        $model->name = 'New name';
        $model->save();

        $this->assertDatabaseMissing('audit_entries', [
            'event' => 'updated',
        ]);
        $this->assertSame(0, AuditEntry::query()->where('event', 'updated')->count());
    }

    public function test_it_masks_configured_fields_on_create(): void
    {
        MaskedModel::query()->create([
            'name' => 'Test invoice',
            'status' => 'pending',
        ]);

        $entry = AuditEntry::query()
            ->where('event', 'created')
            ->sole();

        $this->assertSame(['name' => '********', 'status' => 'pending'], $entry->new_values);
    }

    public function test_it_masks_configured_fields_on_update(): void
    {
        $model = MaskedModel::query()->create([
            'name' => 'Test invoice',
            'status' => 'pending',
        ]);

        $model->name = 'New name';
        $model->save();

        $entry = AuditEntry::query()
            ->where('event', 'updated')
            ->sole();

        $this->assertSame(['name' => '********'], $entry->old_values);
        $this->assertSame(['name' => '********'], $entry->new_values);
    }

    public function test_it_masks_configured_fields_on_delete(): void
    {
        $model = MaskedModel::query()->create([
            'name' => 'Test invoice',
            'status' => 'pending',
        ]);

        $model->delete();

        $entry = AuditEntry::query()
            ->where('event', 'deleted')
            ->sole();

        $this->assertSame(['name' => '********', 'status' => 'pending'], $entry->old_values);
        $this->assertNull($entry->new_values);
    }

    public function test_it_records_a_restored_model(): void
    {
        $model = SoftDeletedModel::query()->create([
            'name' => 'Test invoice',
            'status' => 'pending',
        ]);
        $id = $model->getKey();

        $model->delete();

        $this->assertNull(SoftDeletedModel::query()->find($id));
        $this->assertNotNull(SoftDeletedModel::withTrashed()->find($id));

        $model->restore();

        $entry = AuditEntry::query()
            ->where('event', 'restored')
            ->sole();

        $this->assertSame('Test invoice', $entry->new_values['name']);
        $this->assertSame('pending', $entry->new_values['status']);
        $this->assertArrayNotHasKey('deleted_at', $entry->new_values);
        $this->assertNull($entry->old_values);
        $this->assertNotNull(SoftDeletedModel::query()->find($id));
    }

    public function test_it_records_the_authenticated_actor(): void
    {
        $actor = TestUser::query()->create([
            'name' => 'Test Name',
            'status' => 'active',
        ]);

        $this->actingAs($actor);

        $subject = TestModel::query()->create([
            'name' => 'Test invoice',
            'status' => 'pending',
        ]);

        $entry = AuditEntry::query()
            ->where('subject_id', (string) $subject->getKey())
            ->sole();

        $this->assertSame(
            $actor->getMorphClass(),
            $entry->actor_type,
        );

        $this->assertSame(
            (string) $actor->getKey(),
            $entry->actor_id,
        );

        $this->assertTrue($entry->actor->is($actor));
    }

    public function test_it_records_no_actor_when_the_user_is_not_authenticated(): void
    {
        $subject = TestModel::query()->create([
            'name' => 'Test invoice',
            'status' => 'pending',
        ]);

        $entry = AuditEntry::query()
            ->where('event', 'created')
            ->where('subject_id', (string) $subject->getKey())
            ->sole();

        $this->assertNull($entry->actor_type);
        $this->assertNull($entry->actor_id);
        $this->assertNull($entry->actor);
    }
    public function test_it_records_the_request_context(): void
    {
        $request = Request::create(
            '/',
            'POST',
            server: [
                'REMOTE_ADDR' => '203.0.113.10',
                'HTTP_USER_AGENT' => 'Chrome',
            ],
        );
        $this->app->instance('request', $request);

        $model = TestModel::query()->create([
            'name' => 'Test invoice',
            'status' => 'pending',
        ]);

        $createdEntry = AuditEntry::query()
            ->where('event', 'created')
            ->sole();

        $this->assertSame('203.0.113.10', $createdEntry->ip_address);
        $this->assertSame('Chrome', $createdEntry->user_agent);
        $this->assertTrue(Str::isUuid($createdEntry->request_id));

        $model->status = 'approved';
        $model->save();

        $updatedEntry = AuditEntry::query()
            ->where('event', 'updated')
            ->sole();
        $this->assertSame($createdEntry->request_id, $updatedEntry->request_id);
    }
}
