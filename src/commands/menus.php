<?php

/**
 * If not config file found, call script with actions
 */
if ($config->isConfigured() === false) {
    $data = ['action' => 'config'];

    $workflow->result()
        ->uid('')
        ->arg(json_encode($data))
        ->title('No config file found')
        ->subtitle('Generate and edit the config file')
        ->type('default')
        ->valid(true);

    echo $workflow->output();
    exit();
}

$query = ltrim($argv[2]);

/**
 * If the user wants to edit the config file
 */
if ($query === 'config') {
    $data = ['action' => 'edit'];

    $workflow->result()
        ->uid('')
        ->arg(json_encode($data))
        ->title('Edit config file')
        ->subtitle('Open the config file in your favorite editor!')
        ->type('default')
        ->valid(true);

    echo $workflow->output();
    exit();
}

if ($query === 'sync') {
    $data = ['action' => 'sync'];

    $workflow->result()
        ->uid('')
        ->arg(json_encode($data))
        ->title('Sync projects and tags from online to local cache')
        ->subtitle('Update local projects and tags data')
        ->type('default')
        ->valid(true);

    echo $workflow->output();
    exit();
}

if ($query === 'delete') {
    $data = ['action' => 'delete'];

    $workflow->result()
        ->uid('')
        ->arg(json_encode($data))
        ->title('Delete a timer')
        ->subtitle('Press enter to load recent timers list')
        ->type('default')
        ->valid(true);

    echo $workflow->output();
    exit();
}

if ($query === 'undo') {
    $data['action'] = 'undo';

    $runningServices = $config->runningServices();

    if (empty($runningServices) === true) {
        $workflow->result()
            ->uid('')
            ->arg('')
            ->title('Undo ""')
            ->subtitle('Nothing to undo!')
            ->type('default')
            ->valid(false);
    } else {
        $subtitle = $timer->isRunning() === true ? 'Stop and delete current timer for ' : 'Delete timer for ';
        $subtitle .= implode(' and ', array_map('ucfirst', $runningServices));

        $workflow->result()
            ->uid('')
            ->arg(json_encode($data))
            ->title('Undo "' . $timer->getDescription() . '"')
            ->subtitle($subtitle)
            ->type('default')
            ->valid(true);
    }

    echo $workflow->output();
    exit();
}

if (empty($data) === true) {
    $services = $config->activatedServices();

    if ($timer->isRunning() === true) {
        $data = [
            'action' => 'stop',
            'query'  => $query,
        ];

        if (empty($services) === true) {
            $subtitle = 'No timer services activated. Edit config file to active services';
        } else {
            $subtitle = 'Stop current timer for ' . implode(' and ', array_map('ucfirst', $services));
        }

        $workflow->result()
            ->uid('')
            ->arg(json_encode($data))
            ->title('Stop "' . $timer->getDescription() . '"')
            ->subtitle($subtitle)
            ->type('default')
            ->valid(true);
    } else {
        $data = [
            'action' => 'start',
            'query'  => $query,
        ];

        $continueData = $data;
        $continueData['action'] = 'continue';

        $startAllData = $data;
        $startAllData['action'] = 'start_all';

        $service = $timer->getPrimaryService();

        if (empty($service) === true) {
            $subtitle = 'No timer services activated. Edit config file to active services';
        } else {
            $subtitle = 'Start new timer for ' . ucfirst($service);
        }

        $workflow->result()
            ->uid('')
            ->arg(json_encode($data))
            ->title('Start "' . $query . '"')
            ->subtitle($subtitle)
            ->cmd('Continue a timer', json_encode($continueData))
            ->shift('Start new timer for ' . implode(
                ' and ',
                array_map('ucfirst', $config->activatedServices())),
                json_encode($startAllData)
            )
            ->type('default')
            ->valid(true);
    }

    echo $workflow->output();
    exit();
}

if ($data['show'] === 'project_list') {
    $data['action'] = 'choose_project';
    $data['project_ids'] = null;
    $workflow->result()
        ->arg(json_encode($data))
        ->title('No project')
        ->subtitle('Timer will be created without a project')
        ->type('default')
        ->valid(true);

    $items = $workflowHandler->getProjects();

    foreach ($items as $name => $ids) {
        $subtitle = 'Project available for ' . implode(' and ', array_map(function ($value) {
            return ucfirst($value);
        }, array_keys($ids)));

        $data['project_ids'] = $ids;

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
} elseif ($data['show'] === 'tag_list') {
    $data['action'] = 'choose_tag';
    $data['tag_ids'] = null;
    $workflow->result()
        ->arg(json_encode($data))
        ->title('No tag')
        ->subtitle('Timer will be created without a tag')
        ->type('default')
        ->valid(true);

    $items = $workflowHandler->getTags();

    foreach ($items as $name => $ids) {
        $subtitle = 'Tag available for ' . implode(' and ', array_map(function ($value) {
            return ucfirst($value);
        }, array_keys($ids)));

        $data['tag_ids'] = $ids;
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

$workflow->filterResults($query);
echo $workflow->output();
