<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use AlfredTime\Timer;
use AlfredTime\Config;
use Alfred\Workflows\Workflow;
use AlfredTime\WorkflowHandler;

$workflow = new Workflow();
$config = new Config(getenv('alfred_workflow_data') . '/config.json');
$workflowHandler = new WorkflowHandler($config);

$query = trim($argv[1]);

if (getenv('description') === 'delete') {
    $workflow->result()
        ->title('Choose a timer to delete below')
        ->subtitle('_____________________ BE CAREFUL, NO RECOVERY POSSIBLE _____________________')
        ->type('default')
        ->valid(false);
}

$timers = $workflowHandler->getRecentTimers();

foreach ($timers as $recentTimer) {
    $projectName = $workflowHandler->getProjectName($recentTimer['pid']);
    $tags = $recentTimer['tags'];
    $duration = $recentTimer['duration'];

    $timerData = [
        'id'          => $recentTimer['id'],
        'pid'         => $recentTimer['pid'],
        'tags'        => $recentTimer['tags'],
        'description' => $recentTimer['description'],
    ];

    $subtitle = (empty($projectName) === true ? 'No project' : $projectName) . ', '
        . (empty($tags) === true ? 'No tag' : '[' . implode(', ', $tags) . ']') . ', '
        . ($duration > 0 ? gmdate('H:i:s', $duration) : '--:--:--');

    $workflow->result()
        ->arg(json_encode($timerData))
        ->title($recentTimer['description'])
        ->subtitle($subtitle)
        ->type('default')
        ->icon('icons/toggl.png')
        ->valid(true);
}

$workflow->filterResults($query);

echo $workflow->output();
