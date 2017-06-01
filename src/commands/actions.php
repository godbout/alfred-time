<?php

switch ($data['action']) {
    case 'config':
        $config->generateDefaultConfigurationFile();
        // no break
    case 'edit':
        exec('open "' . getenv('alfred_workflow_data') . '/config.json"');
        break;

    case 'sync':
        $message = $workflowHandler->syncOnlineDataToLocalCache();
        echo $message;
        exit();
        break;

    case 'undo':
        $message = $workflowHandler->getNotification(
            $timer->undo(),
            'undo'
        );
        echo $message;
        exit();
        break;

    case 'stop':
        $message = $workflowHandler->getNotification($timer->stop(), 'stop');
        echo $message;
        exit();
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
        break;
}

echo $message;
