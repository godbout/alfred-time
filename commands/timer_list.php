<?php

require 'vendor/autoload.php';
require 'AlfredTime.class.php';

use Alfred\Workflows\Workflow;

$workflow = new Workflow;
$alfredTime = new AlfredTime;

$query = trim($argv[1]);

$timers = $alfredTime->getRecentTimers();

foreach ($timers as $timer) {
    $workflow->result()
        ->arg($timer['id'])
        ->title($timer['description'])
        ->subtitle('Delete the timer FOREVER')
        ->type('default')
        ->icon('icons/toggl.png')
        ->valid(true);
}

$workflow->filterResults($query);

echo $workflow->output();
