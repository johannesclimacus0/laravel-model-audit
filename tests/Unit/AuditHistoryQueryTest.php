<?php

namespace Johannesclimacus\ModelAudit\Tests\Unit;

use Johannesclimacus\ModelAudit\DTO\AuditHistoryQuery;
use Johannesclimacus\ModelAudit\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use InvalidArgumentException;

class AuditHistoryQueryTest extends TestCase
{
    public function test_it_normalizes_values_and_uses_default_limit(): void
    {
        $history = new AuditHistoryQuery(
            ' App\Models\Invoice ',
            ' 15 ',
            ' updated '
        );

        $this->assertSame('App\Models\Invoice', $history->subjectType);
        $this->assertSame('15', $history->subjectId);
        $this->assertSame('updated', $history->event);
        $this->assertSame(20, $history->limit);
    }

    public function test_it_treats_a_blank_event_as_no_filter(): void
    {
        $history = new AuditHistoryQuery(
            ' App\Models\Invoice ',
            '15',
            '    '
        );

        $this->assertNull($history->event);
    }

    #[DataProvider('invalidQueries')]
    public function test_it_rejects_invalid_values(
        string $subjectType,
        string $subjectId,
        int $limit,
        string $expectedMessage,
    ): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);

        new AuditHistoryQuery(
            subjectType: $subjectType,
            subjectId: $subjectId,
            limit: $limit,
        );
    }

    public static function invalidQueries(): array
    {
        return [
            'empty subject type' => [
                '   ',
                '15',
                20,
                'Subject type cannot be empty.',
            ],
            'empty subject id' => [
                'App\Models\Invoice',
                '   ',
                20,
                'Subject ID cannot be empty.',
            ],
            'limit below minimum' => [
                'App\Models\Invoice',
                '15',
                0,
                'History limit must be between 1 and 100.',
            ],
            'limit above maximum' => [
                'App\Models\Invoice',
                '15',
                101,
                'History limit must be between 1 and 100.',
            ],
        ];
    }
}
