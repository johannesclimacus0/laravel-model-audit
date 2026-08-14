<?php

namespace Local\ModelAudit\Tests\Support;

class MaskedModel extends TestModel
{
    protected $table = 'test_models';

    public function auditMasks(): array
    {
        return ['name' => 'redact'];
    }
}
