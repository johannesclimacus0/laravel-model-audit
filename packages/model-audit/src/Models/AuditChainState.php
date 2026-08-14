<?php

namespace Local\ModelAudit\Models;

use Illuminate\Database\Eloquent\Attributes\DateFormat;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'subject_type',
    'subject_id',
    'last_hash',
    'last_entry_uuid',
    'entries_count',
])]
#[DateFormat('Y-m-d H:i:s.u')]
class AuditChainState extends Model
{
    public function getTable(): string
    {
        return (string) config('model-audit.chain_state_table', 'audit_chain_states');
    }

    public function getConnectionName(): ?string
    {
        $connection = config('model-audit.connection');

        return is_string($connection) ? $connection : parent::getConnectionName();
    }

    protected function casts(): array
    {
        return [
            'entries_count' => 'integer',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }
}
