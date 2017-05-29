<?php

switch ($data['action']) {
    case 'config':
        $config->generateDefaultConfigurationFile();
        exec('open "' . getenv('alfred_workflow_data') . '/config.json"');
        break;

    case 'edit':
        exec('open "' . getenv('alfred_workflow_data') . '/config.json"');
        break;

    case 'sync':
        $message = $workflowHandler->syncOnlineDataToLocalCache();
        echo $message;
        exit();
        break;

    case 'delete':
        $data['original_action'] = $data['action'];
        $data['action'] = 'choose_timer';
        break;

    case 'continue':
        $data['original_action'] = $data['action'];
        $data['action'] = 'choose_timer';
        break;

    case 'choose_timer':
        $data['action'] = 'final';
        break;

    case 'undo':
        $message = $workflowHandler->getNotification(
            $timer->undo(),
            'undo'
        );
        echo $message;
        exit();
        break;

    case 'start':
        $data['original_action'] = $data['action'];
        $data['action'] = 'show_projects';
        break;

    case 'start_all':
        $data['original_action'] = $data['action'];
        $data['action'] = 'show_projects';
        break;

    case 'stop':
        $message = $workflowHandler->getNotification($timer->stop(), 'stop');
        echo $message;
        exit();
        break;

    case 'show_projects':
        $data['action'] = 'show_tags';
        break;

    case 'final':
        if ($data['original_action'] === 'start') {
            $message = $workflowHandler->getNotification(
                $timer->start($data['query'], $data['project_ids'], $data['tag_ids'], $timer->getPrimaryService()),
                'start'
            );
        } elseif ($data['original_action'] === 'start_all') {
            $message = $workflowHandler->getNotification(
                $timer->start($data['query'], $data['project_ids'], $data['tag_ids']),
                'start'
            );
        } elseif ($data['original_action'] === 'delete') {
            $message = $workflowHandler->getNotification(
                $timer->delete([$data['timer_info']['service'] => $data['timer_info']['id']]),
                'delete'
            );
        } elseif ($data['original_action'] === 'continue') {
            $message = $workflowHandler->getNotification(
                $timer->start(
                    $data['timer_info']['description'],
                    [$data['timer_info']['service'] => $data['timer_info']['project_id']],
                    [$data['timer_info']['service'] => $data['timer_info']['tags']],
                    $data['timer_info']['service']),
                'start'
            );
        }

        echo $message;
        exit();
        break;

    default:
        break;
}

echo json_encode($data);
