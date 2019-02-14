<?php

use Godbout\Alfred\ScriptFilter;

require __DIR__ . '/../vendor/autoload.php';

$action = getenv('action');

ScriptFilter::create();

switch ($action) {
    case 'setup':
        require 'setup.php';

        break;

    case 'setup_toggl':
        require 'setup_toggl.php';

        break;

    case 'setup_toggl_apikey':
        require 'setup_toggl_apikey.php';

        break;

    case 'setup_toggl_apikey_save':
        require 'setup_toggl_apikey_save.php';

        break;

    case 'setup_toggl_state':
        require 'setup_toggl_state.php';

        break;

    default:
        require 'default.php';

        break;
}

echo ScriptFilter::output();
