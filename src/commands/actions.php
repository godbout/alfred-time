<?php

switch ($data['action']) {
    case 'setup':
        $config->generateDefaultConfigurationFile();
        // no break
    case 'config':
        exec('open "' . getenv('alfred_workflow_data') . '/config.json"');
        break;

    case 'sync':
        $message = $workflowHandler->syncOnlineDataToLocalCache();
        break;

    case 'undo':
        $message = $workflowHandler->getNotification(
            $timer->undo(),
            'undo'
        );
        break;

    case 'stop':
        $message = $workflowHandler->getNotification($timer->stop(), 'stop');
        break;

    case 'final':
        if ($data['original_action'] === 'start') {
            $message = $workflowHandler->getNotification(
                $timer->start($data['query'], $data['project_ids'], $data['tag_ids'], [$timer->getPrimaryService()]),
                'start'
            );
        } elseif ($data['original_action'] === 'start_all') {
            $message = $workflowHandler->getNotification(
                $timer->start($data['query'], $data['project_ids'], $data['tag_ids'], $config->activatedServices()),
                'start'
            );
        } elseif ($data['original_action'] === 'delete') {
            $message = $workflowHandler->getNotification(
                $timer->delete([$data['timer_info']['service'] => $data['timer_info']['id']]),
                'delete'
            );
        } elseif ($data['original_action'] === 'continue') {
            $timerInfo = $data['timer_info'];
            $message = $workflowHandler->getNotification(
                $timer->start(
                    $timerInfo['description'],
                    [$timerInfo['service'] => $timerInfo['project_id']],
                    [$timerInfo['service'] => $timerInfo['tags']],
                    $timerInfo['service']),
                'start'
            );
        }

        break;
}

echo $message;
