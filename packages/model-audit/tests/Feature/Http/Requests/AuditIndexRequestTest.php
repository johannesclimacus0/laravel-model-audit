<?php

namespace Local\ModelAudit\Tests\Feature\Http\Requests;

use Illuminate\Support\Facades\Validator;
use Local\ModelAudit\Http\Requests\AuditIndexRequest;
use Local\ModelAudit\Tests\TestCase;

class AuditIndexRequestTest extends TestCase
{
    public function test_it_accepts_valid_audit_filters(): void
    {
        $request = new AuditIndexRequest;

        $validator = Validator::make([
            'event' => 'updated',
            'subject_type' => 'App\Models\Invoice',
            'subject_id' => '42',
            'actor_type' => 'App\Models\User',
            'actor_id' => '7',
            'request_id' => 'request-123',
            'date_from' => '2026-08-01',
            'date_to' => '2026-08-16',
        ], $request->rules());

        $this->assertTrue($request->authorize());
        $this->assertFalse($validator->fails());
    }

    public function test_it_rejects_invalid_audit_filters(): void
    {
        $request = new AuditIndexRequest;

        $validator = Validator::make([
            'event' => ['updated'],
            'date_from' => '2026-08-16',
            'date_to' => '2026-08-01',
        ], $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('event', $validator->errors()->toArray());
        $this->assertArrayHasKey('date_to', $validator->errors()->toArray());
    }
}
