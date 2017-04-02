<?php

require 'vendor/autoload.php';
require 'AlfredTime.class.php';

use Alfred\Workflows\Workflow;

$workflow = new Workflow;
$alfredTime = new AlfredTime;

$query = trim($argv[1]);

$tags = $alfredTime->getTags();

$workflow->result()
    ->uid()
    ->arg('')
    ->title('No Tag')
    ->subtitle('Timer will be created without any tag')
    ->type('default')
    ->valid(true);

foreach ($tags as $tag) {
    $workflow->result()
        ->uid()
        ->arg($tag['name'])
        ->title($tag['name'])
        ->subtitle('Toggl tag')
        ->type('default')
        ->valid(true);
}

$workflow->filterResults($query);

echo $workflow->output();
