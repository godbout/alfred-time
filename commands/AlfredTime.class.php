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

    public function getTimerDescription()
    {
        return $this->config['workflow']['timer_description'];
    }

    public function startTimer($description = '', $projectsDefault = null, $tagsDefault = null)
    {
        $message = '';
        $togglId = null;
        $harvestId = null;

        if ($this->isTogglActive() === true) {
            $projectId = isset($projectsDefault['toggl']) ? $projectsDefault['toggl'] : null;
            $tags = isset($tagsDefault['toggl']) ? $tagsDefault['toggl'] : null;

            $message .= $this->startTogglTimer($togglId, $description, $projectId, $tags);
        }

        if ($this->isHarvestActive() === true) {
            $projectId = isset($projectsDefault['harvest']) ? $projectsDefault['harvest'] : null;
            $taskId = isset($tagsDefault['harvest']) ? $tagsDefault['harvest'] : null;

            $message .= "\r\n" . $this->startHarvestTimer($harvestId, $description, $projectId, $taskId);
        }

        $this->config['workflow']['is_timer_running'] = true;
        $this->config['workflow']['timer_toggl_id'] = $togglId;
        $this->config['workflow']['timer_harvest_id'] = $harvestId;
        $this->config['workflow']['timer_description'] = $description;
        $this->saveConfiguration();

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
        $message = '';

        if ($this->isTogglActive() === true) {
            $message .= $this->stopTogglTimer();
        }

        if ($this->isHarvestActive() === true) {
            $message .= "\r\n" . $this->stopHarvestTimer();
        }

        $this->config['workflow']['is_timer_running'] = false;
        $this->saveConfiguration();

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
            array_push($services, 'Toggl');
        }

        if ($this->isHarvestActive() === true) {
            array_push($services, 'Harvest');
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

        $message = $this->deleteTimer();

        return $message;
    }

    private function deleteTimer()
    {
        $message = '';

        $togglId = $this->config['workflow']['timer_toggl_id'];
        $harvestId = $this->config['workflow']['timer_harvest_id'];

        if ($this->isTogglActive() === true) {
            $message .= $this->deleteTogglTimer($togglId);
        }

        if ($this->isHarvestActive() === true) {
            $message .= "\r\n" . $this->deleteHarvestTimer($harvestId);
        }

        $this->config['workflow']['timer_toggl_id'] = $togglId;
        $this->config['workflow']['timer_harvest_id'] = $harvestId;
        $this->config['workflow']['timer_description'] = '';
        $this->saveConfiguration();

        return $message;
    }

    private function deleteTogglTimer(&$togglId)
    {
        $message = '';

        $togglId = $this->config['workflow']['timer_toggl_id'];

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
            $message = '- Could not delete last Toggl timer!';
        } else {
            $togglId = null;
            $message = '- Last Toggl timer deleted';
        }

        return $message;
    }

    private function deleteHarvestTimer(&$harvestId)
    {
        $domain = $this->config['harvest']['domain'];

        $harvestId = $this->config['workflow']['timer_harvest_id'];

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
            $message = '- Could not delete last Harvest timer!';
        } else {
            $harvestId = null;
            $message = '- Last Harvest timer deleted';
        }

        return $message;
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

    private function startTogglTimer(&$togglId, $description, $projectId = null, $tagNames = null)
    {
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
            $togglId = null;
            $message = '- Cannot start Toggl timer!';
        } else {
            $data = json_decode($response, true);
            $togglId = $data['data']['id'];
            $message = '- Toggl timer started';
        }

        return $message;
    }

    private function stopTogglTimer()
    {
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
            $message = '- Could not stop the Toggl timer currently running!';
        } else {
            $message = '- Toggl timer stopped';
        }

        return $message;
    }

    private function startHarvestTimer(&$harvestId, $description, $projectId = null, $taskId = null)
    {
        $domain = $this->config['harvest']['domain'];
        $url = 'https://' . $domain . '.harvestapp.com/daily/add';

        $base64Token = $this->config['harvest']['api_token'];

        $headers = [
            "Content-type: application/json",
            "Accept: application/json",
            'Authorization: Basic ' . $base64Token,
        ];

        // $projectId
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
            $harvestId = null;
            $message = '- Cannot start Harvest timer!';
        } else {
            $data = json_decode($response, true);
            $harvestId = $data['id'];
            $message = '- Harvest timer started';
        }

        return $message;
    }

    private function stopHarvestTimer()
    {
        $domain = $this->config['harvest']['domain'];

        $harvestId = $this->config['workflow']['timer_harvest_id'];

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
            $message = '- Could not stop the Harvest timer currently running!';
        } else {
            $message = '- Harvest timer stopped';
        }

        return $message;
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
