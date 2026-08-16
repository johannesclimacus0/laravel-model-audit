<?php

namespace Johannesclimacus\ModelAudit\Tests\Unit;

use Johannesclimacus\ModelAudit\Hashing\Sha256AuditHasher;
use PHPUnit\Framework\TestCase;

class Sha256AuditHasherTest extends TestCase
{
    private Sha256AuditHasher $hasher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hasher = new Sha256AuditHasher;
    }

    public function test_it_calculates_a_sha256_hash(): void
    {
        $result = $this->hasher->hash('hello');

        $this->assertSame(
            '2cf24dba5fb0a30e26e83b2ac5b9e29e1b161e5c1fa7425e73043362938b9824',
            $result
        );
    }

    public function test_it_returns_the_same_hash_for_the_same_value(): void
    {
        $first = $this->hasher->hash('audit data');
        $second = $this->hasher->hash('audit data');

        $this->assertSame($first, $second);
    }

    public function test_it_returns_different_hashes_for_different_values(): void
    {
        $first = $this->hasher->hash('pending');
        $second = $this->hasher->hash('approved');

        $this->assertNotSame($first, $second);
    }
}
