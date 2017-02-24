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
        ->arg('config')
        ->title('No config file found')
        ->subtitle('Generate and edit the config file')
        ->type('default')
        ->valid(true);
} else {
    if ($query === 'config') {
        $workflow->result()
            ->uid('')
            ->arg('edit')
            ->title('Edit config file')
            ->subtitle('Edit the config file')
            ->type('default')
            ->valid(true);
    } elseif ($alfredTime->hasTimerRunning() === false) {
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
