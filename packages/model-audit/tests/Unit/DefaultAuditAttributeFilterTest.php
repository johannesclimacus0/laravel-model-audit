<?php

namespace Local\ModelAudit\Tests\Unit;

use Local\ModelAudit\Filtering\DefaultAuditAttributeFilter;
use Local\ModelAudit\Tests\Support\SoftDeletedModel;
use Local\ModelAudit\Tests\Support\TestModel;
use Local\ModelAudit\Tests\TestCase;

class DefaultAuditAttributeFilterTest extends TestCase
{
    private DefaultAuditAttributeFilter $filter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filter = new DefaultAuditAttributeFilter;
    }

    public function test_it_excludes_technical_fields_by_default(): void
    {
        $result = $this->filter->filter(new TestModel, $this->values());

        $this->assertSame('Test invoice', $result['name']);
        $this->assertSame('pending', $result['status']);
        $this->assertArrayNotHasKey('id', $result);
        $this->assertArrayNotHasKey('created_at', $result);
        $this->assertArrayNotHasKey('updated_at', $result);
    }

    public function test_it_excludes_model_configured_fields(): void
    {
        $model = new class extends TestModel
        {
            public function auditExclude(): array
            {
                return ['name'];
            }
        };

        $result = $this->filter->filter($model, $this->values());

        $this->assertArrayNotHasKey('name', $result);
    }

    public function test_it_includes_only_model_configured_fields(): void
    {
        $model = new class extends TestModel
        {
            public function auditInclude(): array
            {
                return ['name'];
            }
        };

        $result = $this->filter->filter($model, $this->values());

        $this->assertSame('Test invoice', $result['name']);
        $this->assertArrayNotHasKey('status', $result);
        $this->assertArrayNotHasKey('amount', $result);
        $this->assertArrayNotHasKey('internal_note', $result);
    }

    public function test_include_has_priority(): void
    {
        $model = new class extends TestModel
        {
            public function auditInclude(): array
            {
                return ['name', 'status'];
            }

            public function auditExclude(): array
            {
                return ['name', 'status'];
            }
        };

        $result = $this->filter->filter($model, $this->values());

        $this->assertSame('Test invoice', $result['name']);
        $this->assertSame('pending', $result['status']);
        $this->assertArrayNotHasKey('amount', $result);
        $this->assertArrayNotHasKey('internal_note', $result);
    }

    public function test_it_excludes_soft_deleted_field_by_default(): void
    {
        $model = new SoftDeletedModel;

        $values = ['name' => 'Test name', 'deleted_at' => now()];

        $result = $this->filter->filter($model, $values);

        $this->assertSame(['name' => 'Test name'], $result);
    }

    public function test_it_reads_included_fields_from_model_property(): void
    {
        $model = new class extends TestModel {
            protected array $auditInclude = ['status'];
        };

        $result = $this->filter->filter($model, $this->values());

        $this->assertSame(['status' => 'pending'], $result);
    }

    public function test_it_reads_excluded_fields_from_model_property(): void
    {
        $model = new class extends TestModel {
            protected array $auditExclude = ['internal_note'];
        };

        $result = $this->filter->filter($model, $this->values(),);

        $this->assertSame([
            'name' => 'Test invoice',
            'status' => 'pending',
            'amount' => 1200
        ], $result);
    }

    private function values(): array
    {
        return [
            'id' => 1,
            'name' => 'Test invoice',
            'status' => 'pending',
            'amount' => 1200,
            'internal_note' => 'Test note',
            'created_at' => '2026-08-11 00:00:00',
            'updated_at' => '2026-08-11 00:00:00',
        ];
    }
}
