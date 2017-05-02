<?php

require __DIR__ . '/../vendor/autoload.php';
require 'AlfredTime.class.php';

use Alfred\Workflows\Workflow;

$workflow = new Workflow;
$alfredTime = new AlfredTime;

$query = trim($argv[1]);

$tags = $alfredTime->getTags();

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
