<?php

namespace Local\ModelAudit\Tests\Unit;

use InvalidArgumentException;
use Local\ModelAudit\Masking\DefaultAuditValueMasker;
use Local\ModelAudit\Tests\Support\TestModel;
use Local\ModelAudit\Tests\TestCase;

class DefaultAuditValueMaskerTest extends TestCase
{
    private DefaultAuditValueMasker $masker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->masker = new DefaultAuditValueMasker;
    }

    public function test_it_masks_an_email_address(): void
    {
        $email = new class extends TestModel
        {
            public function auditMasks(): array
            {
                return ['email' => 'email'];
            }
        };
        $values = [
            'email' => 'alex@example.com',
            'status' => 'active',
        ];

        $result = $this->masker->mask($email, $values);

        $this->assertSame([
            'email' => 'al***@example.com',
            'status' => 'active',
        ], $result);
    }

    public function test_it_leaves_only_the_last_four_characters(): void
    {
        $password = new class extends TestModel
        {
            public function auditMasks(): array
            {
                return ['phone' => 'last_four'];
            }
        };
        $values = ['phone' => '+79999999999'];

        $result = $this->masker->mask($password, $values);
        $this->assertSame('********9999', $result['phone']);
    }

    public function test_it_redacts_a_value(): void
    {
        $password = new class extends TestModel
        {
            public function auditMasks(): array
            {
                return ['password' => 'redact'];
            }
        };
        $values = ['password' => 'password'];

        $result = $this->masker->mask($password, $values);

        $this->assertSame('********', $result['password']);
    }

    public function test_it_preserves_null_values(): void
    {
        $email = new class extends TestModel
        {
            public function auditMasks(): array
            {
                return ['email' => 'redact'];
            }
        };
        $values = ['email' => null];

        $result = $this->masker->mask($email, $values);

        $this->assertNull($result['email']);
    }

    public function test_it_redacts_an_invalid_email_address(): void
    {
        $email = new class extends TestModel
        {
            public function auditMasks(): array
            {
                return ['email' => 'email'];
            }
        };
        $values = ['email' => 'invalid'];

        $result = $this->masker->mask($email, $values);

        $this->assertSame('********', $result['email']);
    }

    public function test_it_redacts_a_non_scalar_value(): void
    {
        $arr = new class extends TestModel
        {
            public function auditMasks(): array
            {
                return ['data' => 'redact'];
            }
        };
        $values = [
            'data' => ['login', 'password'],
        ];

        $result = $this->masker->mask($arr, $values);

        $this->assertSame('********', $result['data']);
    }

    public function test_it_completely_masks_a_value_with_four_or_fewer_characters(): void
    {
        $code = new class extends TestModel
        {
            public function auditMasks(): array
            {
                return [
                    'code' => 'last_four',
                ];
            }
        };
        $values = ['code' => '1234'];

        $result = $this->masker->mask($code, $values);

        $this->assertSame('****', $result['code']);
    }

    public function test_it_rejects_an_unknown_masking_strategy(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown masking strategy: wrong.');

        $code = new class extends TestModel
        {
            public function auditMasks(): array
            {
                return [
                    'code' => 'wrong',
                ];
            }
        };
        $values = ['code' => '1234'];

        $this->masker->mask($code, $values);
    }

    public function test_it_translates_an_unknown_strategy_exception_into_russian(): void
    {
        app()->setLocale('ru');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Неизвестная стратегия маскирования: wrong.');

        $code = new class extends TestModel
        {
            public function auditMasks(): array
            {
                return ['code' => 'wrong'];
            }
        };

        $this->masker->mask($code, ['code' => '1234']);
    }
}
