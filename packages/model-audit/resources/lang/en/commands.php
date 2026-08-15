<?php

return [
    'verify' => [
        'valid' => 'Audit chain is valid.',
        'invalid' => 'Audit chain is invalid.',
        'reason' => 'Reason: :reason',
        'entry_uuid' => 'Entry UUID: :uuid',
    ],
    'verify_all' => [
        'none' => 'No audit chains found.',
        'valid' => 'All audit chains are valid.',
        'invalid_chain' => 'Invalid audit chain: :subject_type [:subject_id]',
        'verified' => 'Verified chains: :count',
        'invalid_count' => 'Invalid chains: :count',
    ],
    'status' => [
        'title' => 'Audit status',
        'enabled' => 'Enabled',
        'connection' => 'Connection',
        'entries_table' => 'Entries table',
        'chain_states_table' => 'Chain states table',
        'entries_count' => 'Audit entries',
        'chains_count' => 'Chains',
        'last_entry' => 'Last entry',
        'yes' => 'Yes',
        'no' => 'No',
        'never' => 'never',
    ],
];
