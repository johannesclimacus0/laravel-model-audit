<?php

namespace Local\ModelAudit\Enums;

enum AuditChainFailure: string
{
    case StateMissing = 'state_missing';
    case PreviousHashMismatch = 'previous_hash_mismatch';
    case HashMismatch = 'hash_mismatch';
    case EntryCountMismatch = 'entry_count_mismatch';
    case LastHashMismatch = 'last_hash_mismatch';
    case LastEntryUuidMismatch = 'last_entry_uuid_mismatch';
}
