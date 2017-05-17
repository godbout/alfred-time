<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use AlfredTime\Timer;
use AlfredTime\Config;
use Alfred\Workflows\Workflow;

$workflow = new Workflow();
$config = new Config(getenv('alfred_workflow_data') . '/config.json');
$timer = new Timer($config);

$query = getenv('description');

$tags = $timer->getTags();

if (substr($query, 0, 6) === 'start ') {
    $workflow->result()
        ->arg(json_encode([]))
        ->title('No tag')
        ->subtitle('Timer will be created without any tag')
        ->type('default')
        ->valid(true);

    $tags = array_filter($tags, function ($value) use ($timer) {
        return isset($value[$timer->getPrimaryService() . '_id']);
    });
} elseif (substr($query, 0, 10) === 'start_all ') {
    $activatedServices = $config->activatedServices();

    foreach ($tags as $name => $services) {
        if (count($activatedServices) !== count($services)) {
            unset($tags[$name]);
        }
    }
}

foreach ($tags as $name => $ids) {
    $subtitle = 'Tag available for ' . implode(' and ', array_map(function ($value) {
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
