<?php

require 'vendor/autoload.php';
require 'AlfredTime.class.php';

use Alfred\Workflows\Workflow;

$workflow = new Workflow;
$alfredTime = new AlfredTime;

$query = trim($argv[1]);

/**
 * Check for config file
 * If cannot find, Workflow is not usable
 */

if ($alfredTime->isConfigured() === false) {
    $workflow->result()
        ->uid('')
        ->title('No config file found')
        ->subtitle('Create config file with correct information')
        ->type('default')
        ->valid(false);
} else {
    if ($alfredTime->hasTimerRunning() === false) {
        $workflow->result()
            ->uid('')
            ->arg('start ' . $query)
            ->title('Start "' . $query . '"')
            ->subtitle('Start new timer for Toggl and Harvest')
            ->type('default')
            ->valid(true);
    } else {
        $workflow->result()
            ->uid('')
            ->arg('stop')
            ->title('Stop "' . $alfredTime->getRunningTimerDescription() . '"')
            ->subtitle('Stop current timer for Toggl and Harvest')
            ->type('default')
            ->valid(true);
    }
}

echo $workflow->output();
