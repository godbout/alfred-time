<?php

require 'vendor/autoload.php';
require 'AlfredTime.class.php';

use Alfred\Workflows\Workflow;

$workflow = new Workflow;
$alfredTime = new AlfredTime;

$query = trim($argv[1]);

if (getenv('description') === 'delete') {
    $workflow->result()
        ->title('Choose a timer to delete below')
        ->subtitle('_____________________ BE CAREFUL, NO RECOVERY POSSIBLE _____________________')
        ->type('default')
        ->valid(false);
}

$timers = $alfredTime->getRecentTimers();
$projects = $alfredTime->getProjects();

foreach ($timers as $timer) {

    $projectName = $alfredTime->getProjectName($timer['pid']);
    $tags = $timer['tags'];
    $startTime = $timer['start'];
    $stopTime = $timer['stop'];
    $duration = $timer['duration'];

    $subtitle = (empty($projectName) === true ? 'No project' : $projectName) .', '
        .(empty($tags) === true ? 'No tag' : '[' .implode(', ', $tags) .']') .', '
        .gmdate('H:i:s', $duration);

    $workflow->result()
        ->arg($timer['id'])
        ->title($timer['description'])
        ->subtitle($subtitle)
        ->type('default')
        ->icon('icons/toggl.png')
        ->valid(true);
}

$workflow->filterResults($query);

echo $workflow->output();
