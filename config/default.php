<?php

return [
    'timer' => [
        'primary_service' => 'toggl',
        'is_running' => false,
        'toggl_id' => null,
        'harvest_id' => null,
        'description' => '',
    ],
    'toggl' => [
        'is_active' => true,
        'api_token' => '',
    ],
    'harvest' => [
        'is_active' => false,
        'domain' => '',
        'api_token' => '',
    ],
];
