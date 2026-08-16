<?php

namespace Johannesclimacus\ModelAudit\Tests\Unit;

use Johannesclimacus\ModelAudit\Contracts\AuditCanonicalizer;
use Johannesclimacus\ModelAudit\Contracts\AuditHasher;
use Johannesclimacus\ModelAudit\Contracts\AuditPayloadBuilder;
use Johannesclimacus\ModelAudit\DTO\AuditEntryData;
use Johannesclimacus\ModelAudit\Enums\ModelEvent;
use Johannesclimacus\ModelAudit\Hashing\DefaultAuditHashGenerator;
use Johannesclimacus\ModelAudit\Tests\Support\TestModel;
use Johannesclimacus\ModelAudit\Tests\TestCase;
use Mockery;

class DefaultAuditHashGeneratorTest extends TestCase
{
    public function test_it_builds_canonicalizes_and_hashes_a_payload(): void
    {
        $subject = new TestModel;
        $subject->setAttribute($subject->getKeyName(), 101);
        $subject->exists = true;

        $data = new AuditEntryData(
            subject: $subject,
            event: ModelEvent::Updated,
            oldValues: ['status' => 'pending'],
            newValues: ['status' => 'approved'],
        );

        $uuid = '019ff594-0ce1-71f6-a621-0d4b6139a4d7';
        $previousHash = str_repeat('a', 64);
        $payload = ['event' => 'updated'];
        $canonicalPayload = '{"event":"updated"}';
        $expectedHash = str_repeat('b', 64);

        $payloadBuilder = Mockery::mock(AuditPayloadBuilder::class);
        $payloadBuilder->shouldReceive('build')
            ->once()
            ->with($data, $uuid, $previousHash)
            ->andReturn($payload);

        $canonicalizer = Mockery::mock(AuditCanonicalizer::class);
        $canonicalizer->shouldReceive('canonicalize')
            ->once()
            ->with($payload)
            ->andReturn($canonicalPayload);

        $hasher = Mockery::mock(AuditHasher::class);
        $hasher->shouldReceive('hash')
            ->once()
            ->with($canonicalPayload)
            ->andReturn($expectedHash);

        $generator = new DefaultAuditHashGenerator(
            $payloadBuilder,
            $canonicalizer,
            $hasher,
        );

        $this->assertSame($expectedHash, $generator->generate($data, $uuid, $previousHash));
    }
}
