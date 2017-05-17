<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use AlfredTime\Timer;
use AlfredTime\Config;

$config = new Config(getenv('alfred_workflow_data') . '/config.json');
$time = new Timer($config);

$query = getenv('description');
$message = '';

if (substr($query, 0, 6) === 'config') {
    $config->generateDefaultConfigurationFile();
    exec('open "' . getenv('alfred_workflow_data') . '/config.json"');
} elseif (substr($query, 0, 4) === 'sync') {
    $message = $time->syncOnlineDataToLocalCache();
} elseif (substr($query, 0, 5) === 'edit') {
    exec('open "' . getenv('alfred_workflow_data') . '/config.json"');
} elseif (substr($query, 0, 4) === 'undo') {
    $message = $time->undoTimer();
} elseif (substr($query, 0, 6) === 'delete') {
    /*
     * For now, only handle Toggl
     */
    $timerData = json_decode(getenv('timer_data'), true);
    $message = $time->deleteTimer($timerData['id']);
} elseif (substr($query, 0, 8) === 'continue') {
    $timerData = json_decode(getenv('timer_data'), true);
    $project = ['toggl' => $timerData['pid']];
    $tags = ['toggl' => implode(', ', (empty($timerData['tags']) === true ? [] : $timerData['tags']))];

    $message = $time->startTimer($timerData['description'], $project, $tags);
} elseif (substr($query, 0, 6) === 'start ') {
    $description = substr($query, 6);

    $projectData = json_decode(getenv('project_data'), true);
    $tagData = json_decode(getenv('tag_data'), true);
    $message = $time->startTimer($description, $projectData, $tagData, $config->get('workflow', 'primary_service'));
} elseif (substr($query, 0, 10) === 'start_all ') {
    $description = substr($query, 10);

    $projectData = json_decode(getenv('project_data'), true);
    $tagData = json_decode(getenv('tag_data'), true);
    $message = $time->startTimer($description, $projectData, $tagData);
    
} elseif (substr($query, 0, 4) === 'stop') {
    $message = $time->stopRunningTimer();
}

echo $message;
