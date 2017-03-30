<?php

require 'AlfredTime.class.php';

$alfredTime = new AlfredTime;

$query = getenv('description');
$message = '';

if (substr($query, 0, 6) === 'config') {
    $alfredTime->generateDefaultConfigurationFile();
    exec('open "' . getenv('alfred_workflow_data') . '/config.json"');
} elseif (substr($query, 0, 4) === 'sync') {
    $message = $alfredTime->syncOnlineDataToLocalCache();
} elseif (substr($query, 0, 5) === 'edit') {
    exec('open "' . getenv('alfred_workflow_data') . '/config.json"');
} elseif (substr($query, 0, 6) === 'start ') {
    $description = substr($query, 6);
    $message = $alfredTime->startTimer($description, getenv('project_id'), getenv('tag_name'));
} elseif (substr($query, 0, 14) === 'start_default ') {
    $description = substr($query, 14);
    $message = $alfredTime->startTimerWithDefaultOptions($description);
} elseif (substr($query, 0, 4) === 'stop') {
    $message = $alfredTime->stopRunningTimer();
}

echo $message;
