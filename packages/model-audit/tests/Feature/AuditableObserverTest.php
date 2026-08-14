<?php

namespace Local\ModelAudit\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Local\ModelAudit\Models\AuditEntry;
use Local\ModelAudit\Tests\Support\ExcludedFieldsModel;
use Local\ModelAudit\Tests\Support\MaskedModel;
use Local\ModelAudit\Tests\Support\TestModel;
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
}
