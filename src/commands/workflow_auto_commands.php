<?php

$services = $config->activatedServices();

if ($timer->isRunning() === true) {
    $data['action'] = 'stop';
    $data['query'] = $query;

    if (empty($services) === true) {
        $subtitle = 'No timer services activated. Edit config file to active services';
    } else {
        $subtitle = 'Stop current timer for ' . implode(' and ', array_map('ucfirst', $services));
    }

    $workflow->result()
        ->uid('')
        ->arg(json_encode($data))
        ->title('Stop "' . $timer->getDescription() . '"')
        ->subtitle($subtitle)
        ->type('default')
        ->valid(true);
} else {
    $data['action'] = 'start';
    $data['query'] = $query;

    $continueData = $data;
    $continueData['action'] = 'continue';

    $startAllData = $data;
    $startAllData['action'] = 'start_all';

    $service = $timer->getPrimaryService();

    if (empty($service) === true) {
        $subtitle = 'No timer services activated. Edit config file to active services';
    } else {
        $subtitle = 'Start new timer for ' . ucfirst($service);
    }

    $workflow->result()
        ->uid('')
        ->arg(json_encode($data))
        ->title('Start "' . $query . '"')
        ->subtitle($subtitle)
        ->cmd('Continue a timer', json_encode($continueData))
        ->shift('Start new timer for ' . implode(
            ' and ',
            array_map('ucfirst', $config->activatedServices())),
            json_encode($startAllData)
        )
        ->type('default')
        ->valid(true);
}

echo $workflow->output();
