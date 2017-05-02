<?php

require 'Toggl.class.php';
require 'Harvest.class.php';

class AlfredTime
{
    private $config;
    private $message;
    private $currentImplementation = [
        'start' => ['toggl'],
        'start_default' => ['toggl', 'harvest'],
        'stop' => ['toggl', 'harvest'],
        'delete' => ['toggl'],
    ];
    private $toggl;
    private $harvest;

    public function __construct()
    {
        $this->config = $this->loadConfiguration();
        $this->message = '';
        $this->toggl = new Toggl($this->config['toggl']['api_token']);
        $this->harvest = new Harvest($this->config['harvest']['domain'], $this->config['harvest']['api_token']);
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

    public function startTimer($description = '', $projectsDefault = null, $tagsDefault = null, $startDefault = false)
    {
        $message = '';
        $startType = $startDefault === true ? 'start_default' : 'start';
        $atLeastOneServiceStarted = false;
        $implementedServices = $this->implementedServicesForFeature($startType);

        /**
         * When starting a new timer, all the services timer IDs have to be put to null
         * so that when the user uses the UNDO feature, it doesn't delete old previous
         * other services timers. The timer IDs are used for the UNDO feature and
         * should then contain the IDs of the last starts through the workflow, not
         * through each individual service
         */
        if (empty($implementedServices) === false) {
            foreach ($this->activatedServices() as $service) {
                $this->config['workflow']['timer_' . $service . '_id'] = null;
                $this->saveConfiguration();
            }
        }

        foreach ($implementedServices as $service) {
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

        return $this->startTimer($description, $projectsDefault, $tagsDefault, true);
    }

    public function stopRunningTimer()
    {
        $message = '';
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

    public function servicesToUndo()
    {
        $services = [];

        foreach ($this->activatedServices() as $service) {
            if ($this->config['workflow']['timer_' . $service . '_id'] !== null) {
                array_push($services, $service);
            }
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
        foreach ($this->servicesToUndo() as $service) {
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

        $atLeastOneTimerDeleted = false;
        foreach ($this->implementedServicesForFeature('delete') as $service) {
            $functionName = 'delete' . ucfirst($service) . 'Timer';
            if (call_user_func_array(['AlfredTime', $functionName], [$timerId]) === true) {
                $this->config['workflow']['timer_' . $service . '_id'] = null;
                $atLeastOneTimerDeleted = true;
            }

            $message .= $this->getLastMessage() . "\r\n";
        }

        if ($atLeastOneTimerDeleted === true) {
            $this->config['workflow']['is_timer_running'] = false;
            $this->saveConfiguration();
        }

        return $message;
    }

    public function implementedServicesForFeature($feature = null)
    {
        $services = [];

        if (isset($this->currentImplementation[$feature]) === true) {
            $services = $this->currentImplementation[$feature];
        }

        return $services;
    }

    public function getProjectName($projectId)
    {
        $projectName = '';

        $projects = $this->getProjects();
        foreach ($projects as $project) {
            if ($project['id'] === $projectId) {
                $projectName = $project['name'];
                break;
            }
        }

        return $projectName;
    }

    private function getRecentTogglTimers()
    {
        return $this->toggl->getRecentTimers();
    }

    private function getLastMessage()
    {
        return $this->message;
    }

    private function deleteTogglTimer($togglId)
    {
        $res = $this->toggl->deleteTimer($togglId);
        $this->message = $this->toggl->getLastMessage();

        return $res;
    }

    private function deleteHarvestTimer($harvestId)
    {
        $res = $this->harvest->deleteTimer($harvestId);
        $this->message = $this->harvest->getLastMessage();

        return $res;
    }

    private function syncTogglOnlineDataToLocalCache()
    {
        $data = $this->toggl->getOnlineData();
        $this->message = $this->toggl->getLastMessage();

        if (empty($data) === false) {
            $this->saveTogglDataCache($data);
        }

        return $this->message;
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
        $togglId = $this->toggl->startTimer($description, $projectId, $tagNames);
        $this->message = $this->toggl->getLastMessage();

        return $togglId;
    }

    private function stopTogglTimer()
    {
        $togglId = $this->config['workflow']['timer_toggl_id'];

        $res = $this->toggl->stopTimer($togglId);
        $this->message = $this->toggl->getLastMessage();

        return $res;
    }

    private function startHarvestTimer($description, $projectId, $taskId)
    {
        $harvestId = $this->harvest->startTimer($description, $projectId, $taskId);
        $this->message = $this->harvest->getLastMessage();

        return $harvestId;
    }

    private function stopHarvestTimer()
    {
        $harvestId = $this->config['workflow']['timer_harvest_id'];

        $res = $this->harvest->stopTimer($harvestId);
        $this->message = $this->harvest->getLastMessage();

        return $res;
    }

    private function getTogglProjects()
    {
        $cacheData = [];
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
        $cacheData = [];

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
