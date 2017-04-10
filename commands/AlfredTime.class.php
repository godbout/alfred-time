<?php

class AlfredTime
{
    private $config;
    private $message;

    public function __construct()
    {
        $this->config = $this->loadConfiguration();
        $this->message = '';
    }

    public function isConfigured()
    {
        return $this->config === null ? false : true;
    }

    public function hasTimerRunning()
    {
        return $this->config['workflow']['is_timer_running'] === false ? false : true;
    }

    public function getTimerDescription()
    {
        return $this->config['workflow']['timer_description'];
    }

    public function startTimer($description = '', $projectsDefault = null, $tagsDefault = null)
    {
        $atLeastOneServiceStarted = false;

        foreach ($this->activatedServices() as $service) {
            $defaultProjectId = isset($projectsDefault[$service]) ? $projectsDefault[$service] : null;
            $defaultTags = isset($tagsDefault[$service]) ? $tagsDefault[$service] : null;

            $functionName = 'start' . ucfirst($service) . 'Timer';
            $timerId = call_user_func_array(['AlfredTime', $functionName], [$description, $defaultProjectId, $defaultTags]);
            $this->config['workflow']['timer_' . $service . '_id'] = $timerId;
            if ($timerId !== null) {
                $atLeastOneServiceStarted = true;
            }

            $message .= $this->getLastMessage() . "\r\n";
        }

        if ($atLeastOneServiceStarted === true) {
            $this->config['workflow']['timer_description'] = $description;
            $this->config['workflow']['is_timer_running'] = true;
            $this->saveConfiguration();
        }

        return $message;
    }

    public function startTimerWithDefaultOptions($description)
    {
        $projectsDefault = [
            'toggl' => $this->config['toggl']['default_project_id'],
            'harvest' => $this->config['harvest']['default_project_id'],
        ];

        $tagsDefault = [
            'toggl' => $this->config['toggl']['default_tags'],
            'harvest' => $this->config['harvest']['default_task_id'],
        ];

        return $this->startTimer($description, $projectsDefault, $tagsDefault);
    }

    public function stopRunningTimer()
    {
        $atLeastOneServiceStopped = false;

        foreach ($this->activatedServices() as $service) {
            $functionName = 'stop' . ucfirst($service) . 'Timer';
            if (call_user_func(['AlfredTime', $functionName]) === true) {
                $atLeastOneServiceStopped = true;
            }
            $message .= $this->getLastMessage() . "\r\n";
        }

        if ($atLeastOneServiceStopped === true) {
            $this->config['workflow']['is_timer_running'] = false;
            $this->saveConfiguration();
        }

        return $message;
    }

    public function generateDefaultConfigurationFile()
    {
        $this->config = [
            'workflow' => [
                'is_timer_running' => false,
                'timer_toggl_id' => null,
                'timer_harvest_id' => null,
                'timer_description' => '',
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
            array_push($services, 'toggl');
        }

        if ($this->isHarvestActive() === true) {
            array_push($services, 'harvest');
        }

        return $services;
    }

    public function syncOnlineDataToLocalCache()
    {
        $message = '';

        if ($this->isTogglActive() === true) {
            $message .= $this->syncTogglOnlineDataToLocalCache();
        }

        return $message;
    }

    public function getProjects()
    {
        $projects = [];

        if ($this->isTogglActive() === true) {
            $projects = array_merge($projects, $this->getTogglProjects());
        }

        return $projects;
    }

    public function getTags()
    {
        $tags = [];

        if ($this->isTogglActive() === true) {
            $tags = array_merge($tags, $this->getTogglTags());
        }

        return $tags;
    }

    public function UndoTimer()
    {
        $message = '';

        if ($this->hasTimerRunning() === true) {
            $this->stopRunningTimer();
        }

        $atLeastOneTimerDeleted = false;
        foreach ($this->activatedServices() as $service) {
            $functionName = 'delete' . ucfirst($service) . 'Timer';
            if (call_user_func_array(['AlfredTime', $functionName], [$this->config['workflow']['timer_' . $service . '_id']]) === true) {
                $this->config['workflow']['timer_' . $service . '_id'] = null;
                $atLeastOneTimerDeleted = true;
            }

            $message .= $this->getLastMessage() . "\r\n";
        }

        if ($atLeastOneTimerDeleted === true) {
            $this->saveConfiguration();
        }

        return $message;
    }

    public function getRecentTimers()
    {
        $timers = [];

        if ($this->isTogglActive() === true) {
            $timers = array_merge($timers, $this->getRecentTogglTimers());
        }

        return $timers;
    }

    public function deleteTimer($timerId)
    {
        $message = '';

        if ($this->isTogglActive() === true) {
            $this->deleteTogglTimer($timerId);
            $message .= $this->getLastMessage() . "\r\n";
        }

        return $message;
    }

    private function getRecentTogglTimers()
    {
        $timers = [];

        $url = 'https://www.toggl.com/api/v8/time_entries';

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
        $lastHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response !== false && $lastHttpCode === 200) {
            $timers = json_decode($response, true);
        }

        return array_reverse($timers);
    }

    private function getLastMessage()
    {
        return $this->message;
    }

    private function deleteTogglTimer($togglId)
    {
        $res = false;

        $url = 'https://www.toggl.com/api/v8/time_entries/' . $togglId;

        $apiToken = $this->config['toggl']['api_token'];

        $headers = [
            "Content-type: application/json",
            "Accept: application/json",
            'Authorization: Basic ' . base64_encode($apiToken . ':api_token'),
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $lastHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $lastHttpCode !== 200) {
            $this->message = '- Could not delete Toggl timer!';
        } else {
            $this->message = '- Toggl timer deleted';
            $res = true;
        }

        return $res;
    }

    private function deleteHarvestTimer($harvestId)
    {
        $res = false;

        $domain = $this->config['harvest']['domain'];

        $url = 'https://' . $domain . '.harvestapp.com/daily/delete/' . $harvestId;

        $base64Token = $this->config['harvest']['api_token'];

        $headers = [
            "Content-type: application/json",
            "Accept: application/json",
            'Authorization: Basic ' . $base64Token,
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $lastHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $lastHttpCode !== 200) {
            $this->message = '- Could not delete Harvest timer!';
        } else {
            $this->message = '- Harvest timer deleted';
            $res = true;
        }

        return $res;
    }

    private function syncTogglOnlineDataToLocalCache()
    {
        $url = 'https://www.toggl.com/api/v8/me?with_related_data=true';

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
        $lastHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || ($lastHttpCode < 200 || $lastHttpCode > 299)) {
            $message = '- Cannot get Toggl online data!';
        } else {
            $this->saveTogglDataCache(json_decode($response, true));
            $message = '- Toggl data cached';
        }

        return $message;
    }

    private function saveTogglDataCache($data)
    {
        $cacheFile = getenv('alfred_workflow_data') . '/toggl_cache.json';
        file_put_contents($cacheFile, json_encode($data));
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

    private function startTogglTimer($description, $projectId, $tagNames)
    {
        $togglId = null;

        $url = 'https://www.toggl.com/api/v8/time_entries/start';

        $apiToken = $this->config['toggl']['api_token'];

        $headers = [
            "Content-type: application/json",
            "Accept: application/json",
            'Authorization: Basic ' . base64_encode($apiToken . ':api_token'),
        ];

        $item = [
            'time_entry' => [
                'description' => $description,
                'pid' => $projectId,
                'tags' => explode(', ', $tagNames),
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
            $this->message = '- Cannot start Toggl timer!';
        } else {
            $data = json_decode($response, true);
            $togglId = $data['data']['id'];
            $this->message = '- Toggl timer started';
        }

        return $togglId;
    }

    private function stopTogglTimer()
    {
        $res = false;

        $message = '';

        $togglId = $this->config['workflow']['timer_toggl_id'];

        $url = 'https://www.toggl.com/api/v8/time_entries/' . $togglId . '/stop';

        $apiToken = $this->config['toggl']['api_token'];

        $headers = [
            "Content-type: application/json",
            "Accept: application/json",
            'Authorization: Basic ' . base64_encode($apiToken . ':api_token'),
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            $this->message = '- Could not stop Toggl timer!';
        } else {
            $this->message = '- Toggl timer stopped';
            $res = true;
        }

        return $res;
    }

    private function startHarvestTimer($description, $projectId, $taskId)
    {
        $harvestId = null;

        $domain = $this->config['harvest']['domain'];
        $url = 'https://' . $domain . '.harvestapp.com/daily/add';

        $base64Token = $this->config['harvest']['api_token'];

        $headers = [
            "Content-type: application/json",
            "Accept: application/json",
            'Authorization: Basic ' . $base64Token,
        ];

        $item = [
            'notes' => $description,
            'project_id' => $projectId,
            'task_id' => $taskId,
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($item, true));
        $response = curl_exec($ch);
        $lastHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $lastHttpCode !== 201) {
            $this->message = '- Cannot start Harvest timer!';
        } else {
            $data = json_decode($response, true);
            $harvestId = $data['id'];
            $this->message = '- Harvest timer started';
        }

        return $harvestId;
    }

    private function stopHarvestTimer()
    {
        $res = false;

        $harvestId = $this->config['workflow']['timer_harvest_id'];

        if ($this->isHarvestTimerRunning($harvestId) === true) {
            $domain = $this->config['harvest']['domain'];

            $url = 'https://' . $domain . '.harvestapp.com/daily/timer/' . $harvestId;

            $base64Token = $this->config['harvest']['api_token'];

            $headers = [
                "Content-type: application/json",
                "Accept: application/json",
                'Authorization: Basic ' . $base64Token,
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            $lastHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($response === false || $lastHttpCode !== 200) {
                $this->message = '- Could not stop Harvest timer!';
            } else {
                $this->message = '- Harvest timer stopped';
                $res = true;
            }
        } else {
            $this->message = '- Harvest timer was not running';
        }

        return $res;
    }

    private function isHarvestTimerRunning($harvestId)
    {
        $res = false;

        $domain = $this->config['harvest']['domain'];

        $harvestId = $this->config['workflow']['timer_harvest_id'];

        $url = 'https://' . $domain . '.harvestapp.com/daily/show/' . $harvestId;

        $base64Token = $this->config['harvest']['api_token'];

        $headers = [
            "Content-type: application/json",
            "Accept: application/json",
            'Authorization: Basic ' . $base64Token,
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $lastHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response !== false && $lastHttpCode === 200) {
            $data = json_decode($response, true);
            if (isset($data['timer_started_at']) === true) {
                $res = true;
            }
        }

        return $res;
    }

    private function getTogglProjects()
    {
        $cacheFile = getenv('alfred_workflow_data') . '/toggl_cache.json';

        if (file_exists($cacheFile)) {
            $cacheData = json_decode(file_get_contents($cacheFile), true);
        }

        /**
         * To only show projects that are currently active
         * The Toggl API is slightly weird on that
         */
        foreach ($cacheData['data']['projects'] as $key => $project) {
            if (isset($project['server_deleted_at']) === true) {
                unset($cacheData['data']['projects'][$key]);
            }
        }

        return $cacheData['data']['projects'];
    }

    private function getTogglTags()
    {
        $cacheFile = getenv('alfred_workflow_data') . '/toggl_cache.json';

        if (file_exists($cacheFile)) {
            $cacheData = json_decode(file_get_contents($cacheFile), true);
        }

        return $cacheData['data']['tags'];
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
