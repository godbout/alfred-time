<?php

$valid = true;
$userQueryCommandFound = true;

switch ($query) {
    case 'config':
        $title = 'Edit config file';
        $subtitle = 'Open the config file in your favorite editor!';

        break;

    case 'sync':
        $title = 'Sync projects and tags from online to local cache';
        $subtitle = 'Update local projects and tags data';

        break;

    case 'delete':
        $title = 'Delete a timer';
        $subtitle = 'Press enter to load recent timers list';

        break;

    case 'undo':
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

    default:
        $userQueryCommandFound = false;

        break;
}

if ($userQueryCommandFound === true) {
    $data['action'] = $query;

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
