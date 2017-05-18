<?php

require_once __DIR__ . '/../../vendor/autoload.php';

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

foreach ($timers as $service => $recentTimers) {
    foreach ($recentTimers as $recentTimer) {
        $projectName = is_int($recentTimer['project_name'])
            ? $workflowHandler->getProjectName($service, $recentTimer['project_name'])
            : $recentTimer['project_name'];
        $tags = $recentTimer['tags'];
        $duration = $recentTimer['duration'];

        $timerData = [
            'service'     => $service,
            'description' => $recentTimer['description'],
            'id'          => $recentTimer['id'],
            'project_id'  => $recentTimer['project_id'],
            'tags'        => $recentTimer['tags'],
        ];

        $subtitle = (empty($projectName) === true ? 'No project' : $projectName) . ', '
            . (empty($tags) === true ? 'No tag' : '[' . $tags . ']') . ', '
            . ($duration > 0 ? gmdate('H:i:s', $duration) : '--:--:--');

        $workflow->result()
            ->arg(json_encode($timerData))
            ->title(empty($recentTimer['description']) ? '' : $recentTimer['description'])
            ->subtitle($subtitle)
            ->type('default')
            ->icon('icons/' . $service . '.png')
            ->valid(true);
    }
}

$workflow->filterResults($query);

echo $workflow->output();
