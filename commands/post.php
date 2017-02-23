<?php

require 'AlfredTime.class.php';

$alfredTime = new AlfredTime;

$query = trim($argv[1]);
$message = '';

if (substr($query, 0, 6) === 'start ') {
    $description = substr($query, 6);
    $alfredTime->startTimer($description);
} elseif (substr($query, 0, 4) === 'stop') {
    $alfredTime->stopRunningTimer();
}

echo $query;