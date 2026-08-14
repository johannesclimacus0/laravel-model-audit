<?php

return [
    'enabled' => env('MODEL_AUDIT_ENABLED', true),

    'connection' => null,

    'table' => 'audit_entries',

    'chain_state_table' => 'audit_chain_states',

    'context' => [
        'ip_address' => env('MODEL_AUDIT_CAPTURE_IP_ADDRESS', true),
        'user_agent' => env('MODEL_AUDIT_CAPTURE_USER_AGENT', true),
        'request_id' => env('MODEL_AUDIT_CAPTURE_REQUEST_ID', true),
    ],
];
