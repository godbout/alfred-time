<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use AlfredTime\Config;
use AlfredTime\WorkflowHandler;

$config = new Config(getenv('alfred_workflow_data') . '/config.json');
$workflowHandler = new WorkflowHandler($config);

$items = $workflowHandler->getTags();

include_once __DIR__ . '/item_list.php';
