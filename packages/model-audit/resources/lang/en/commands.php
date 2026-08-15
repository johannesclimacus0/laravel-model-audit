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
];
