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

/**
 * First thing we do is check what kind of action is called
 *
 * Is it showing an Alfred menu? Or executing an action?
 * Below is the code for menus
 */
if ($type === 'menus') {
    require_once 'menus.php';

/**
 * If an action is requested
 */
} elseif ($type === 'actions') {
    require_once 'actions.php';
}
