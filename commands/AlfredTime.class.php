<?php

class AlfredTime
{
    private $config;

    public function __construct()
    {
        $this->config = $this->loadConfiguration();
    }

    public function isConfigured()
    {
        return $this->config === null ? false : true;
    }

    public function hasTimerRunning()
    {
        return $this->config['workflow']['is_timer_running'] === false ? false : true;
    }

    public function getRunningTimerDescription()
    {
        $description = '';

        if ($this->config['workflow']['is_timer_running'] === true) {
            $description = $this->config['workflow']['current_timer_description'];
        }

        return $description;
    }

    public function startTimer($description = '')
    {
        $message = '';

        if ($this->isTogglActive() === true) {
            $message .= $this->startTogglTimer($description);
        }

        if ($this->isHarvestActive() === true) {
            $message .= "\r\n" . $this->startHarvestTimer($description);
        }

        $this->config['workflow']['is_timer_running'] = true;
        $this->config['workflow']['current_timer_description'] = $description;
        $this->saveConfiguration();

        return $message;
    }

    public function stopRunningTimer()
    {
        $message = '';

        if ($this->isTogglActive() === true) {
            $message .= $this->stopTogglTimer();
        }

        if ($this->isHarvestActive() === true) {
            $message .= "\r\n" . $this->stopHarvestTimer();
        }

        $this->config['workflow']['is_timer_running'] = false;
        $this->config['workflow']['current_timer_description'] = '';
        $this->saveConfiguration();

        return $message;
    }

    public function generateDefaultConfigurationFile()
    {
        $this->config = [
            'workflow' => [
                'is_timer_running' => false,
                'current_timer_description' => '',
            ],
            'toggl' => [
                'is_active' => true,
                'api_token' => '',
                'default_project_id' => '',
                'default_tags' => '',
            ],
            'harvest' => [
                'is_active' => true,
                'domain' => '',
                'api_token' => '',
                'default_project_id' => '',
                'default_task_id' => '',
            ],
        ];

        $this->saveConfiguration();
    }

    public function activatedServices()
    {
        $services = [];

        if ($this->isTogglActive() === true) {
            array_push($services, 'Toggl');
        }

        if ($this->isHarvestActive() === true) {
            array_push($services, 'Harvest');
        }

        return $services;
    }

    private function loadConfiguration()
    {
        $config = null;
        $configFile = getenv('alfred_workflow_data') . '/config.json';

        if (file_exists($configFile)) {
            $config = json_decode(file_get_contents($configFile), true);
        }

        return $config;
    }

    private function saveConfiguration()
    {
        $workflowDir = getenv('alfred_workflow_data');
        $configFile = $workflowDir . '/config.json';

        if (file_exists($workflowDir) === false) {
            mkdir($workflowDir);
        }
        
        file_put_contents($configFile, json_encode($this->config, JSON_PRETTY_PRINT));
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
        $lastHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || ($lastHttpCode < 200 || $lastHttpCode > 299)) {
            $message = '- Cannot start Toggl timer!';
        } else {
            $message = '- Toggl timer started';
        }

        return $message;
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
                $message = '- No Toggl timer currently running!';
            } else {
                $currentTimerId = $data['data']['id'];

                curl_close($ch);

                $url = 'https://www.toggl.com/api/v8/time_entries/' . $currentTimerId . '/stop';
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                curl_close($ch);

                if ($response === false) {
                    $message = '- Could not stop the Toggl timer currently running!';
                } else {
                    $message = '- Toggl timer stopped';
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
        $lastHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || ($lastHttpCode < 200 || $lastHttpCode > 299)) {
            $message = '- Cannot start Harvest timer!';
        } else {
            $message = '- Harvest timer started';
        }

        return $message;
    }

    private function stopHarvestTimer()
    {
        $domain = $this->config['harvest']['domain'];
        $url = 'https://' . $domain . '.harvestapp.com/daily?slim=1';

        $base64Token = $this->config['harvest']['api_token'];

        $headers = [
            "Content-type: application/json",
            "Accept: application/json",
            'Authorization: Basic ' . $base64Token,
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
            if (empty($data['day_entries']) === true) {
                $message = '- No Harvest timer currently running!';
            } else {
                $currentTimerId = end($data['day_entries'])['id'];

                curl_close($ch);
                $url = 'https://' . $domain . '.harvestapp.com/daily/timer/' . $currentTimerId;
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                curl_close($ch);

                if ($response === false) {
                    $message = '- Could not stop the Harvest timer currently running!';
                } else {
                    $message = '- Harvest timer stopped';
                }
            }
        }

        return $message;
    }

    private function getTogglProjects()
    {
        $url = 'https://www.toggl.com/api/v8/me?with_related_data=true';

        $apiToken = $this->config['toggl']['api_token'];

        $headers = [
            "Content-type: application/json",
            "Accept: application/json",
            'Authorization: Basic ' . base64_encode($apiToken . ':api_token'),
        ];


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
        // $response = curl_exec($ch);
        $lastHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || ($lastHttpCode < 200 || $lastHttpCode > 299)) {
            $message = '- Cannot start Toggl timer!';
        } else {
            $message = '- Toggl timer started';
        }

        return $message;
    }

    private function isTogglActive()
    {
        return $this->config['toggl']['is_active'];
    }

    private function isHarvestActive()
    {
        return $this->config['harvest']['is_active'];
    }
}
