<?php

$valid = true;
$userQueryCommandFound = false;

switch ($query) {
    case 'config':
        $userQueryCommandFound = true;
        $data['action'] = 'edit';
        $title = 'Edit config file';
        $subtitle = 'Open the config file in your favorite editor!';
        break;

    case 'sync':
        $userQueryCommandFound = true;
        $data['action'] = 'sync';
        $title = 'Sync projects and tags from online to local cache';
        $subtitle = 'Update local projects and tags data';
        break;

    case 'delete':
        $userQueryCommandFound = true;
        $data['action'] = 'delete';
        $title = 'Delete a timer';
        $subtitle = 'Press enter to load recent timers list';
        break;

    case 'undo':
        $userQueryCommandFound = true;
        $data['action'] = 'undo';

        $runningServices = $config->runningServices();

        if (empty($runningServices) === true) {
            $title = 'Undo ""';
            $subtitle = 'Nothing to undo!';
            $valid = false;
        } else {
            $title = 'Undo "' . $timer->getDescription() . '"';
            $subtitle = $timer->isRunning() === true ? 'Stop and delete current timer for ' : 'Delete timer for ';
            $subtitle .= implode(' and ', array_map('ucfirst', $runningServices));
        }

        break;
}

if ($userQueryCommandFound === true) {
    $workflow->result()
        ->uid('')
        ->arg(json_encode($data))
        ->title($title)
        ->subtitle($subtitle)
        ->type('default')
        ->valid($valid);

    echo $workflow->output();
    exit();
}
