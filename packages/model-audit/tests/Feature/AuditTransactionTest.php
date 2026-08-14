<?php

namespace Local\ModelAudit\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Local\ModelAudit\Tests\Support\TestModel;
use Local\ModelAudit\Tests\TestCase;

class AuditTransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_rolls_back_the_audit_entry_with_the_model_transaction(): void
    {
        DB::beginTransaction();

        $model = TestModel::query()->create([
            'name' => 'Test invoice',
            'status' => 'pending',
        ]);

        $id = $model->getKey();

        $this->assertDatabaseHas('test_models', ['id' => $id]);
        $this->assertDatabaseHas('audit_entries', [
            'subject_type' => $model->getMorphClass(),
            'subject_id' => (string) $id,
        ]);

        DB::rollBack();

        $this->assertDatabaseMissing('test_models', ['id' => $id]);
        $this->assertDatabaseMissing('audit_entries', [
            'subject_type' => $model->getMorphClass(),
            'subject_id' => (string) $id,
        ]);
    }
}
