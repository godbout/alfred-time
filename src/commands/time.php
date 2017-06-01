<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use AlfredTime\Timer;
use AlfredTime\Config;
use Alfred\Workflows\Workflow;
use AlfredTime\WorkflowHandler;

$workflow = new Workflow();
$config = new Config(getenv('alfred_workflow_data') . '/config.json');
$timer = new Timer($config);
$workflowHandler = new WorkflowHandler($config);

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
