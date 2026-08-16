<?php

namespace Johannesclimacus\ModelAudit\Tests\Unit;

use Carbon\CarbonImmutable;
use InvalidArgumentException;
use Johannesclimacus\ModelAudit\DTO\AuditLogQuery;
use PHPUnit\Framework\TestCase;

class AuditLogQueryTest extends TestCase
{
    public function test_it_normalizes_blank_filters(): void
    {
        $query = new AuditLogQuery(
            event: ' updated ',
            subjectType: ' ',
            subjectId: '',
            actorType: null,
            requestId: ' request-1 ',
        );

        $this->assertSame('updated', $query->event);
        $this->assertNull($query->subjectType);
        $this->assertNull($query->subjectId);
        $this->assertNull($query->actorType);
        $this->assertSame('request-1', $query->requestId);
    }

    public function test_it_rejects_an_invalid_page_size(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new AuditLogQuery(perPage: 101);
    }

    public function test_it_rejects_an_invalid_date_range(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new AuditLogQuery(
            dateFrom: CarbonImmutable::parse('2026-08-16'),
            dateTo: CarbonImmutable::parse('2026-08-15'),
        );
    }
}
