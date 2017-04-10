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
            ->subtitle('Open the config file in your favorite editor!')
            ->type('default')
            ->valid(true);
    } elseif ($query === 'sync') {
        $workflow->result()
            ->uid('')
            ->arg('sync')
            ->title('Sync projects and tags from online to local cache')
            ->subtitle('Update local projects and tags data')
            ->type('default')
            ->valid(true);
    } elseif ($query === 'undo') {
        $servicesToUndo = $alfredTime->servicesToUndo();

        if (empty($servicesToUndo) === true) {
            $workflow->result()
                ->uid('')
                ->arg('')
                ->title('Undo ""')
                ->subtitle('Nothing to undo!')
                ->type('default')
                ->valid(false);
        } else {
            $subtitle = $alfredTime->hasTimerRunning() === true ? 'Stop and delete current timer for ' : 'Delete timer for ';
            foreach ($servicesToUndo as $service) {
                if ($service === reset($servicesToUndo)) {
                    $subtitle .= ucfirst($service);
                } else {
                    $subtitle .= ' and ' . ucfirst($service);
                }
            }

            $workflow->result()
                ->uid('')
                ->arg('undo')
                ->title('Undo "' . $alfredTime->getTimerDescription() . '"')
                ->subtitle($subtitle)                
                ->type('default')
                ->valid(true);
        }
    } elseif ($query === 'delete') {
        $workflow->result()
            ->uid('')
            ->arg('delete')
            ->title('Delete a timer')
            ->subtitle('Press enter to load recent timers list')
            ->type('default')
            ->valid(true);
    } elseif ($alfredTime->hasTimerRunning() === false) {
        $services = $alfredTime->activatedServices();

        if (empty($services) === true) {
            $subtitle = 'No timer services activated. Edit config file to active services';
        } else {
            $subtitle = 'Start new timer for ';
            foreach ($services as $service) {
                if ($service === reset($services)) {
                    $subtitle .= ucfirst($service);
                } else {
                    $subtitle .= ' and ' . ucfirst($service);
                }
            }
        }

        $workflow->result()
            ->uid('')
            ->arg('start ' . $query)
            ->title('Start "' . $query . '"')
            ->subtitle($subtitle)
            ->type('default')
            ->mod('cmd', $subtitle . ' with default project and tags', 'start_default ' . $query)
            ->valid(true);
    } else {
        $services = $alfredTime->activatedServices();

        if (empty($services) === true) {
            $subtitle = 'No timer services activated. Edit config file to active services';
        } else {
            $subtitle = 'Stop current timer for ';
            foreach ($services as $service) {
                if ($service === reset($services)) {
                    $subtitle .= $service;
                } else {
                    $subtitle .= ' and ' . $service;
                }
            }
        }

        $workflow->result()
            ->uid('')
            ->arg('stop')
            ->title('Stop "' . $alfredTime->getTimerDescription() . '"')
            ->subtitle($subtitle)
            ->type('default')
            ->valid(true);
    }
}

echo $workflow->output();