<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use AlfredTime\Timer;
use AlfredTime\Toggl;
use AlfredTime\Config;
use AlfredTime\Harvest;
use Alfred\Workflows\Workflow;
use AlfredTime\WorkflowHandler;

$workflow = new Workflow();
$config = new Config(getenv('alfred_workflow_data') . '/config.json');
$harvest = new Harvest(
    $config->get('harvest', 'domain'),
    $config->get('harvest', 'api_token')
);
$toggl = new Toggl(
    $config->get('toggl', 'api_token')
);
$timer = new Timer($config, $toggl, $harvest);
$workflowHandler = new WorkflowHandler($config, $toggl, $harvest);

$type = trim($argv[1]);
$data = json_decode(getenv('data'), true);

switch ($type) {
    case 'menus':
        require_once 'menus.php';
        break;

    case 'dispatch':
        require_once 'dispatch.php';
        break;

    case 'actions':
        require_once 'actions.php';
        break;
}
