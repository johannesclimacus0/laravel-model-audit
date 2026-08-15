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
    'show' => [
        'title' => 'Audit history: :subject_type [:subject_id]',
        'none' => 'No audit entries found.',
        'system' => 'system',
        'invalid_limit_integer' => 'Limit must be an integer.',
        'invalid_limit_range' => 'Limit must be between 1 and :max.',
        'subject_type_required' => 'Subject type cannot be empty.',
        'subject_id_required' => 'Subject ID cannot be empty.',
        'json_encoding_failed' => 'Unable to encode audit history as JSON.',
        'headers' => [
            'uuid' => 'UUID',
            'event' => 'Event',
            'actor' => 'Actor',
            'created_at' => 'Created',
        ],
    ],
];
