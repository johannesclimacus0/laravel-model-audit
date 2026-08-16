<?php

namespace Johannesclimacus\ModelAudit\Canonicalization;

use Johannesclimacus\ModelAudit\Contracts\AuditCanonicalizer;

class JsonAuditCanonicalizer implements AuditCanonicalizer
{
    public function canonicalize(array $payload): string
    {
        $payload = $this->sortRecursively($payload);

        return json_encode($payload,
            JSON_THROW_ON_ERROR
            | JSON_UNESCAPED_UNICODE
            | JSON_UNESCAPED_SLASHES
            | JSON_PRESERVE_ZERO_FRACTION
        );
    }

    private function sortRecursively(array $values): array
    {
        foreach ($values as $key => $value) {
            if (is_array($value)) {
                $values[$key] = $this->sortRecursively($value);
            }
        }
        if (!array_is_list($values)) {
            ksort($values, SORT_STRING);
        }

        return $values;
    }
}
