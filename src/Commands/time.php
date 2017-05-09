<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use AlfredTime\Time;

$time = new Time();

$query = getenv('description');
$message = '';

if (substr($query, 0, 6) === 'config') {
    $time->generateDefaultConfigurationFile();
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

    /*
     * For now, only handle Toggl projects and tags
     */
    $project = [
        'toggl' => getenv('project_id'),
    ];

    $tag = [
        'toggl' => getenv('tag_name'),
    ];

    $message = $time->startTimer($description, $project, $tag);
} elseif (substr($query, 0, 14) === 'start_default ') {
    $description = substr($query, 14);
    $message = $time->startTimerWithDefaultOptions($description);
} elseif (substr($query, 0, 4) === 'stop') {
    $message = $time->stopRunningTimer();
}

echo $message;
