<?php

require 'AlfredTime.class.php';

$alfredTime = new AlfredTime;

$query = trim($argv[1]);
$message = '';

if (substr($query, 0, 6) === 'config') {
    $alfredTime->generateDefaultConfigurationFile();
    exec('open "' . getenv('alfred_workflow_data') . '/config.json"');
} elseif (substr($query, 0, 5) === 'edit') {
    exec('open "' . getenv('alfred_workflow_data') . '/config.json"');
} elseif (substr($query, 0, 6) === 'start ') {
    $description = substr($query, 6);
    $message = $alfredTime->startTimer($description);
} elseif (substr($query, 0, 4) === 'stop') {
    $message = $alfredTime->stopRunningTimer();
}

echo $message;
