<?php

namespace Local\ModelAudit\Tests\Unit;

use Local\ModelAudit\Canonicalization\JsonAuditCanonicalizer;
use PHPUnit\Framework\TestCase;

class JsonAuditCanonicalizerTest extends TestCase
{
    private JsonAuditCanonicalizer $canonicalizer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->canonicalizer = new JsonAuditCanonicalizer;
    }

    public function test_it_sorts_associative_array_keys(): void
    {
        $result = $this->canonicalizer->canonicalize([
            'status' => 'approved',
            'amount' => 100,
        ]);

        $this->assertSame(
            '{"amount":100,"status":"approved"}',
            $result
        );
    }

    public function test_it_sorts_nested_associative_arrays(): void
    {
        $result = $this->canonicalizer->canonicalize([
            'event' => 'updated',
            'new_values' => [
                'status' => 'approved',
                'amount' => 100,
            ],
        ]);

        $this->assertSame(
            '{"event":"updated","new_values":{"amount":100,"status":"approved"}}',
            $result
        );
    }

    public function test_it_preserves_list_order(): void
    {
        $result = $this->canonicalizer->canonicalize([
            'events' => [
                'updated',
                'created',
                'deleted',
            ],
        ]);

        $this->assertSame(
            '{"events":["updated","created","deleted"]}',
            $result
        );
    }
}
