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

    'ui' => [
        'enabled' => env('MODEL_AUDIT_UI_ENABLED', true),

        'prefix' => env('MODEL_AUDIT_UI_PREFIX', 'audit'),

        'route_name_prefix' => 'model-audit.',

        'middleware' => [
            'web',
        ],

        'ability' => 'viewModelAudit',

        'per_page' => 8,

        'subject_limit' => 100,

        'layout' => 'model-audit::layout',

        'vite_assets' => [
            'resources/css/app.css',
        ],
    ],
];
