<?php

namespace Johannesclimacus\ModelAudit\Masking;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Johannesclimacus\ModelAudit\Contracts\AuditValueMasker;

class DefaultAuditValueMasker implements AuditValueMasker
{
    private const REDACTED_VALUE = '********';

    public function mask(Model $model, array $values): array
    {
        $rules = $model->auditMasks();

        foreach ($rules as $field => $strategy) {
            if (!array_key_exists($field, $values)) {
                continue;
            }

            $values[$field] = $this->maskValue($values[$field], $strategy);
        }

        return $values;
    }

    private function maskValue(mixed $value, string $strategy): mixed
    {
        if ($value === null) {
            return null;
        }

        if (!is_scalar($value)) {
            return self::REDACTED_VALUE;
        }

        $value = (string) $value;

        return match ($strategy) {
            'redact' => self::REDACTED_VALUE,
            'email' => $this->maskEmail($value),
            'last_four' => $this->maskLastFour($value),
            default => throw new InvalidArgumentException(
                __('model-audit::exceptions.unknown_masking_strategy', ['strategy' => $strategy]),
            ),
        };
    }

    private function maskEmail(string $value): string
    {
        $parts = explode('@', $value, 2);

        if (count($parts) !== 2 || $parts[0] === '' || $parts[1] === '') {
            return self::REDACTED_VALUE;
        }

        $name = $parts[0];
        $domain = $parts[1];

        $visibleLength = min(2, mb_strlen($name));
        $visibleName = mb_substr($name, 0, $visibleLength);

        return $visibleName . '***@' . $domain;
    }

    private function maskLastFour(string $value): string
    {
        $length = mb_strlen($value);

        if ($length <= 4) {
            return str_repeat('*', $length);
        }

        $hiddenLength = $length - 4;

        $visiblePart = mb_substr($value, -4);

        return str_repeat('*', $hiddenLength) . $visiblePart;
    }
}
