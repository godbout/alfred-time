<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use AlfredTime\Timer;
use AlfredTime\Config;
use AlfredTime\WorkflowHandler;

$config = new Config(getenv('alfred_workflow_data') . '/config.json');
$workflowHandler = new WorkflowHandler($config);
$timer = new Timer($config);

$query = getenv('description');
$message = '';

if (substr($query, 0, 6) === 'config') {
    $config->generateDefaultConfigurationFile();
    exec('open "' . getenv('alfred_workflow_data') . '/config.json"');
} elseif (substr($query, 0, 4) === 'sync') {
    $message = $workflowHandler->syncOnlineDataToLocalCache();
} elseif (substr($query, 0, 5) === 'edit') {
    exec('open "' . getenv('alfred_workflow_data') . '/config.json"');
} elseif (substr($query, 0, 4) === 'undo') {
    $message = $workflowHandler->getNotification(
        $timer->undo(),
        'undo'
    );
} elseif (substr($query, 0, 6) === 'delete') {
    $message = $workflowHandler->getNotification(
        $timer->delete(json_decode(getenv('timer_data'), true)),
        'delete'
    );
} elseif (substr($query, 0, 8) === 'continue') {
    // $timerData = json_decode(getenv('timer_data'), true);
    // $project = ['toggl' => $timerData['pid']];
    // $tags = ['toggl' => implode(', ', (empty($timerData['tags']) === true ? [] : $timerData['tags']))];

    // $message = $timer->start($timerData['description'], $project, $tags);
} elseif (substr($query, 0, 6) === 'start ') {
    $description = substr($query, 6);

    $projectData = json_decode(getenv('project_data'), true);
    $tagData = json_decode(getenv('tag_data'), true);

    $message = $workflowHandler->getNotification(
        $timer->start($description, $projectData, $tagData, $timer->getPrimaryService()),
        'start'
    );
} elseif (substr($query, 0, 10) === 'start_all ') {
    $description = substr($query, 10);

    $projectData = json_decode(getenv('project_data'), true);
    $tagData = json_decode(getenv('tag_data'), true);
    $message = $workflowHandler->getNotification(
        $timer->start($description, $projectData, $tagData),
        'start'
    );
} elseif (substr($query, 0, 4) === 'stop') {
    $message = $workflowHandler->getNotification(
        $timer->stop(),
        'stop'
    );
}

echo $message;
