<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use AlfredTime\Timer;
use AlfredTime\Config;
use Alfred\Workflows\Workflow;
use AlfredTime\WorkflowHandler;

$workflow = new Workflow();
$config = new Config(getenv('alfred_workflow_data') . '/config.json');
$timer = new Timer($config);
$workflowHandler = new WorkflowHandler($config);

$query = getenv('description');

$items = call_user_func([$workflowHandler, 'get' . ucfirst($argv[1])]);

if (substr($query, 0, 6) === 'start ') {
    $workflow->result()
        ->arg(json_encode([]))
        ->title('No ' . getItemName($argv[1]))
        ->subtitle('Timer will be created without a ' . getItemName($argv[1]))
        ->type('default')
        ->valid(true);

    $items = array_filter($items, function ($value) use ($timer) {
        return isset($value[$timer->getPrimaryService() . '_id']);
    });
} elseif (substr($query, 0, 10) === 'start_all ') {
    $activatedServices = $config->activatedServices();

    foreach ($items as $name => $services) {
        if (count($activatedServices) !== count($services)) {
            unset($items[$name]);
        }
    }
}

foreach ($items as $name => $ids) {
    $subtitle = ucfirst(getItemName($argv[1])) . ' available for ' . implode(' and ', array_map(function ($value) {
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

/**
 * @param $name
 */
function getItemName($name)
{
    return substr($name, 0, -1);
}
