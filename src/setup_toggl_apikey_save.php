<?php

use Godbout\Alfred\Item;
use Godbout\Alfred\ScriptFilter;

$config = [
    'toggl' => [
        'api_token' => getenv('toggl_apikey'),
    ]
];

file_put_contents(
    __DIR__ . '/../tests/AlfredWorkflowDataFolderMock/config.json',
    json_encode($config, JSON_PRETTY_PRINT)
);

ScriptFilter::add(
    Item::create()
        ->title('API KEY SAVED!')
        ->subtitle('You can just press Enter.')
        ->arg('notification'),
    Item::create()
        ->title('Back')
        ->subtitle('Go back to Toggl Setup')
        ->arg('setup_toggl')
);
