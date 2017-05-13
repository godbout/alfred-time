<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use AlfredTime\Time;
use Alfred\Workflows\Workflow;
use AlfredTime\Config;

$workflow = new Workflow();
$config = new Config(getenv('alfred_workflow_data') . '/config.json');
$time = new Time($config);

$query = trim($argv[1]);

$tags = $time->getTags();

$workflow->result()
    ->arg('')
    ->title('No tag')
    ->subtitle('Timer will be created without any tag')
    ->type('default')
    ->valid(true);

foreach ($tags as $tag) {
    $workflow->result()
        ->arg($tag['name'])
        ->title($tag['name'])
        ->subtitle('Toggl tag')
        ->type('default')
        ->icon('icons/toggl.png')
        ->valid(true);
}

$workflow->filterResults($query);

echo $workflow->output();
