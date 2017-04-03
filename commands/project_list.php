<?php

require 'vendor/autoload.php';
require 'AlfredTime.class.php';

use Alfred\Workflows\Workflow;

$workflow = new Workflow;
$alfredTime = new AlfredTime;

$query = trim($argv[1]);

$projects = $alfredTime->getProjects();

$workflow->result()
    ->arg('')
    ->title('No project')
    ->subtitle('Timer will be created without a project')
    ->type('default')
    ->valid(true);

foreach ($projects as $project) {
    $workflow->result()
        ->arg($project['id'])
        ->title($project['name'])
        ->subtitle('Toggl project')
        ->type('default')
        ->valid(true);
}

$workflow->filterResults($query);

echo $workflow->output();
