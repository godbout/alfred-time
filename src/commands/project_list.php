<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use AlfredTime\Timer;
use AlfredTime\Config;
use Alfred\Workflows\Workflow;

$workflow = new Workflow();
$config = new Config(getenv('alfred_workflow_data') . '/config.json');
$timer = new Timer($config);

$query = getenv('description');

$projects = $timer->getProjects();

if (substr($query, 0, 6) === 'start ') {
    $workflow->result()
        ->arg(json_encode([]))
        ->title('No project')
        ->subtitle('Timer will be created without a project')
        ->type('default')
        ->valid(true);

    $projects = array_filter($projects, function ($value) use ($timer) {
        return isset($value[$timer->getPrimaryService() . '_id']);
    });
} elseif (substr($query, 0, 10) === 'start_all ') {
    $activatedServices = $config->activatedServices();

    foreach ($projects as $name => $services) {
        if (count($activatedServices) !== count($services)) {
            unset($projects[$name]);
        }
    }
}

foreach ($projects as $name => $ids) {
    $subtitle = 'Project available for ' . implode(' and ', array_map(function ($value) {
        return substr(ucfirst($value), 0, -3);
    }, array_keys($ids)));

    $item = $workflow->result()
        ->arg(json_encode($ids))
        ->title($name)
        ->subtitle($subtitle)
        ->type('default')
        ->valid(true);

    if (count($ids) === 1) {
        $item->icon('icons/' . substr(key($ids), 0, -3) . '.png');
    }
}

echo $workflow->output();
