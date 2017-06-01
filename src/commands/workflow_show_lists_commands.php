<?php

if (in_array($data['show'], ['project_list', 'tag_list']) === true) {
    if ($data['show'] === 'project_list') {
        $data['action'] = 'choose_project';
        $itemIds = 'project_ids';
        $itemName = 'Project';
        $items = $workflowHandler->getProjects();
    } elseif ($data['show'] === 'tag_list') {
        $data['action'] = 'choose_tag';
        $itemIds = 'tag_ids';
        $itemName = 'Tag';
        $items = $workflowHandler->getTags();
    }

    $data[$itemIds] = null;
    
    $workflow->result()
        ->arg(json_encode($data))
        ->title('No ' .lcfirst($itemName))
        ->subtitle('Timer will be created without a ' .lcfirst($itemName))
        ->type('default')
        ->valid(true);

    foreach ($items as $name => $ids) {
        $subtitle = $itemName . ' available for ' . implode(' and ', array_map(function ($value) {
            return ucfirst($value);
        }, array_keys($ids)));

        $data[$itemIds] = $ids;

        $item = $workflow->result()
            ->arg(json_encode($data))
            ->title($name)
            ->subtitle($subtitle)
            ->type('default')
            ->valid(true);

        if (count($ids) === 1) {
            $item->icon('icons/' . key($ids) . '.png');
        }
    }
} elseif ($data['show'] === 'timer_list') {
    $data['action'] = 'final';

    if ($data['original_action'] === 'delete') {
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

            $data['timer_info'] = [
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
                ->arg(json_encode($data))
                ->title(empty($recentTimer['description']) ? '' : $recentTimer['description'])
                ->subtitle($subtitle)
                ->type('default')
                ->icon('icons/' . $service . '.png')
                ->valid(true);
        }
    }
}
