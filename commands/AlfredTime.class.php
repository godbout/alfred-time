<?php

class AlfredTime
{
    private $config;

    public function __construct()
    {
        $this->config = $this->getConfiguration();
    }

    public function isConfigured()
    {
        return $this->config === null ? false : true;
    }

    public function hasTimerRunning()
    {
        return $this->config['workflow']['is_timer_running'] === '0' ? false : true;
    }

    public function getRunningTimerDescription()
    {
        $description = '';

        if ($this->config['workflow']['is_timer_running'] === '1') {
            $description = $this->config['workflow']['current_timer_description'];
        }

        return $description;
    }

    public function startTimer($description = '')
    {
        if ($this->config['toggl']['is_active'] === '1') {
            $this->startTogglTimer($description);
        }

        if ($this->config['harvest']['is_active'] === '1') {
            $this->startHarvestTimer($description);
        }
    }

    public function stopRunningTimer()
    {
        if ($this->config['toggl']['is_active'] === '1') {
            $this->stopTogglTimer();
        }

        if ($this->config['harvest']['is_active'] === '1') {
            $this->stopHarvestTimer();
        }
    }

    private function getConfiguration()
    {
        $config = null;
        $configFile = getenv('alfred_workflow_data') . '/config.json';

        if (file_exists($configFile)) {
            $config = json_decode(file_get_contents($configFile), true);
        }

        return $config;
    }

    private function startTogglTimer($description)
    {
        $url = 'https://www.toggl.com/api/v8/time_entries/start';

        $apiToken = $this->config['toggl']['api_token'];

        $headers = [
            "Content-type: application/json",
            "Accept: application/json",
            'Authorization: Basic ' . base64_encode($apiToken . ':api_token'),
        ];

        $defaultProjectId = $this->config['toggl']['default_project_id'];
        $defaultTags = explode(', ', $this->config['toggl']['default_tags']);

        $item = [
            'time_entry' => [
                'description' => $description,
                'pid' => $defaultProjectId,
                'tags' => $defaultTags,
                'created_with' => 'Alfred Time Workflow',
            ],
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($item, true));
        $response = curl_exec($ch);
        curl_close($ch);
    }

    private function stopTogglTimer()
    {
        $message = '';

        $url = 'https://www.toggl.com/api/v8/time_entries/current';

        $apiToken = $this->config['toggl']['api_token'];

        $headers = [
            "Content-type: application/json",
            "Accept: application/json",
            'Authorization: Basic ' . base64_encode($apiToken . ':api_token'),
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        if ($response === false) {
            $message = curl_error($ch);
            curl_close($ch);
        } else {
            $data = json_decode($response, true);
            /**
             * There was no timer running
             */
            if (empty($data['data']) === true) {
                $message = 'No Toggl timer currently running!';
            } else {
                $currentTimerId = $data['data']['id'];

                curl_close($ch);

                $url = 'https://www.toggl.com/api/v8/time_entries/' . $currentTimerId . '/stop';
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);

                if ($response === false) {
                    $message = curl_error($ch);
                    curl_close($ch);
                } else {
                    if (empty($data['data']) === true) {
                        $message = 'Could not stop the Toggl timer currently running!';
                    } else {
                        $message = 'Toggl timer stopped!';
                    }
                    curl_close($ch);
                }
            }
        }

        return $message;
    }

    private function startHarvestTimer($description)
    {
        $domain = $this->config['harvest']['domain'];
        $url = 'https://' . $domain . '.harvestapp.com/daily/add';

        $base64Token = $this->config['harvest']['api_token'];

        $headers = [
            "Content-type: application/json",
            "Accept: application/json",
            'Authorization: Basic ' . $base64Token,
        ];

        $defaultProjectId = $this->config['harvest']['default_project_id'];
        $defaultTaskId = $this->config['harvest']['default_task_id'];

        $item = [
            'notes' => $description,
            'project_id' => $defaultProjectId,
            'task_id' => $defaultTaskId,
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($item, true));
        $response = curl_exec($ch);
        curl_close($ch);
    }

    private function stopHarvestTimer()
    {
    }
}
