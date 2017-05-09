<?php

require_once 'Config.php';
require_once 'Toggl.class.php';
require_once 'Harvest.class.php';

class AlfredTime
{
    /**
     * @var mixed
     */
    private $config;

    /**
     * @var array
     */
    private $currentImplementation = [
        'start'         => ['toggl'],
        'start_default' => ['toggl', 'harvest'],
        'stop'          => ['toggl', 'harvest'],
        'delete'        => ['toggl'],
    ];

    /**
     * @var mixed
     */
    private $harvest;

    /**
     * @var mixed
     */
    private $message;

    /**
     * @var mixed
     */
    private $toggl;

    public function __construct()
    {
        $this->config = new Config(getenv('alfred_workflow_data') . '/config.json');
        $this->toggl = new Toggl($this->config->get('toggl', 'api_token'));
        $this->harvest = new Harvest($this->config->get('harvest', 'domain'), $this->config->get('harvest', 'api_token'));
        $this->message = '';
    }

    /**
     * @return mixed
     */
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

    /**
     * @param  $timerId
     * @return string
     */
    public function deleteTimer($timerId)
    {
        $message = '';

        $atLeastOneTimerDeleted = false;

        foreach ($this->implementedServicesForFeature('delete') as $service) {
            $functionName = 'delete' . ucfirst($service) . 'Timer';

            if (call_user_func_array(['AlfredTime', $functionName], [$timerId]) === true) {
                if ($timerId === $this->config->get('workflow', 'timer_' . $service . '_id')) {
                    $this->config->update('workflow', 'timer_' . $service . '_id', null);
                    $atLeastOneTimerDeleted = true;
                }
            }

            $message .= $this->getLastMessage() . "\r\n";
        }

        if ($atLeastOneTimerDeleted === true) {
            $this->config->update('workflow', 'is_timer_running', false);
        }

        return $message;
    }

    public function generateDefaultConfigurationFile()
    {
        $this->config->generateDefaultConfigurationFile();
    }

    /**
     * @param  $projectId
     * @return mixed
     */
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

    /**
     * @return mixed
     */
    public function getProjects()
    {
        $projects = [];

        if ($this->isTogglActive() === true) {
            $projects = array_merge($projects, $this->getTogglProjects());
        }

        return $projects;
    }

    /**
     * @return mixed
     */
    public function getRecentTimers()
    {
        $timers = [];

        if ($this->isTogglActive() === true) {
            $timers = array_merge($timers, $this->getRecentTogglTimers());
        }

        return $timers;
    }

    /**
     * @return mixed
     */
    public function getTags()
    {
        $tags = [];

        if ($this->isTogglActive() === true) {
            $tags = array_merge($tags, $this->getTogglTags());
        }

        return $tags;
    }

    /**
     * @return mixed
     */
    public function getTimerDescription()
    {
        return $this->config->get('workflow', 'timer_description');
    }

    /**
     * @return mixed
     */
    public function hasTimerRunning()
    {
        return $this->config->get('workflow', 'is_timer_running') === false ? false : true;
    }

    /**
     * @param  $feature
     * @return mixed
     */
    public function implementedServicesForFeature($feature = null)
    {
        $services = [];

        if (isset($this->currentImplementation[$feature]) === true) {
            $services = $this->currentImplementation[$feature];
        }

        return $services;
    }

    /**
     * @return mixed
     */
    public function isConfigured()
    {
        return $this->config === null ? false : true;
    }

    /**
     * @return mixed
     */
    public function servicesToUndo()
    {
        $services = [];

        foreach ($this->activatedServices() as $service) {
            if ($this->config->get('workflow', 'timer_' . $service . '_id') !== null) {
                array_push($services, $service);
            }
        }

        return $services;
    }

    /**
     * @param  $description
     * @param  $projectsDefault
     * @param  null               $tagsDefault
     * @param  boolean            $startDefault
     * @return mixed
     */
    public function startTimer($description = '', $projectsDefault = null, $tagsDefault = null, $startDefault = false)
    {
        $message = '';
        $startType = $startDefault === true ? 'start_default' : 'start';
        $atLeastOneServiceStarted = false;
        $implementedServices = $this->implementedServicesForFeature($startType);

/*
 * When starting a new timer, all the services timer IDs have to be put to null
 * so that when the user uses the UNDO feature, it doesn't delete old previous
 * other services timers. The timer IDs are used for the UNDO feature and
 * should then contain the IDs of the last starts through the workflow, not
 * through each individual sefrvice
 */
        if (empty($implementedServices) === false) {
            foreach ($this->activatedServices() as $service) {
                $this->config->update('workflow', 'timer_' . $service . '_id', null);
            }
        }

        foreach ($implementedServices as $service) {
            $defaultProjectId = isset($projectsDefault[$service]) ? $projectsDefault[$service] : null;
            $defaultTags = isset($tagsDefault[$service]) ? $tagsDefault[$service] : null;

            $functionName = 'start' . ucfirst($service) . 'Timer';
            $timerId = call_user_func_array(['AlfredTime', $functionName], [$description, $defaultProjectId, $defaultTags]);
            $this->config->update('workflow', 'timer_' . $service . '_id', $timerId);

            if ($timerId !== null) {
                $atLeastOneServiceStarted = true;
            }

            $message .= $this->getLastMessage() . "\r\n";
        }

        if ($atLeastOneServiceStarted === true) {
            $this->config->update('workflow', 'timer_description', $description);
            $this->config->update('workflow', 'is_timer_running', true);
        }

        return $message;
    }

    /**
     * @param  $description
     * @return mixed
     */
    public function startTimerWithDefaultOptions($description)
    {
        $projectsDefault = [
            'toggl'   => $this->config->get('toggl', 'default_project_id'),
            'harvest' => $this->config->get('harvest', 'default_project_id'),
        ];

        $tagsDefault = [
            'toggl'   => $this->config->get('toggl', 'default_tags'),
            'harvest' => $this->config->get('harvest', 'default_task_id'),
        ];

        return $this->startTimer($description, $projectsDefault, $tagsDefault, true);
    }

    /**
     * @return mixed
     */
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
            $this->config->update('workflow', 'is_timer_running', false);
        }

        return $message;
    }

    /**
     * @return mixed
     */
    public function syncOnlineDataToLocalCache()
    {
        $message = '';

        if ($this->isTogglActive() === true) {
            $message .= $this->syncTogglOnlineDataToLocalCache();
        }

        return $message;
    }

    /**
     * @return mixed
     */
    public function undoTimer()
    {
        $message = '';

        if ($this->hasTimerRunning() === true) {
            $this->stopRunningTimer();
        }

        $atLeastOneTimerDeleted = false;

        foreach ($this->servicesToUndo() as $service) {
            $functionName = 'delete' . ucfirst($service) . 'Timer';

            if (call_user_func_array(['AlfredTime', $functionName], [$this->config->get('workflow', 'timer_' . $service . '_id')]) === true) {
                $this->config->update('workflow', 'timer_' . $service . '_id', null);
                $atLeastOneTimerDeleted = true;
            }

            $message .= $this->getLastMessage() . "\r\n";
        }

        if ($atLeastOneTimerDeleted === true) {
            $this->config->update('workflow', 'is_timer_running', false);
        }

        return $message;
    }

    /**
     * @param  $harvestId
     * @return mixed
     */
    private function deleteHarvestTimer($harvestId)
    {
        $res = $this->harvest->deleteTimer($harvestId);
        $this->message = $this->harvest->getLastMessage();

        return $res;
    }

    /**
     * @param  $togglId
     * @return mixed
     */
    private function deleteTogglTimer($togglId)
    {
        $res = $this->toggl->deleteTimer($togglId);
        $this->message = $this->toggl->getLastMessage();

        return $res;
    }

    /**
     * @return mixed
     */
    private function getLastMessage()
    {
        return $this->message;
    }

    /**
     * @return mixed
     */
    private function getRecentTogglTimers()
    {
        return $this->toggl->getRecentTimers();
    }

    /**
     * @return mixed
     */
    private function getTogglProjects()
    {
        $cacheData = [];
        $cacheFile = getenv('alfred_workflow_data') . '/toggl_cache.json';

        if (file_exists($cacheFile)) {
            $cacheData = json_decode(file_get_contents($cacheFile), true);
        }

/*
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

    /**
     * @return mixed
     */
    private function getTogglTags()
    {
        $cacheFile = getenv('alfred_workflow_data') . '/toggl_cache.json';
        $cacheData = [];

        if (file_exists($cacheFile)) {
            $cacheData = json_decode(file_get_contents($cacheFile), true);
        }

        return $cacheData['data']['tags'];
    }

    /**
     * @return mixed
     */
    private function isHarvestActive()
    {
        return $this->config->get('harvest', 'is_active');
    }

    /**
     * @return mixed
     */
    private function isTogglActive()
    {
        return $this->config->get('toggl', 'is_active');
    }

    /**
     * @param $data
     */
    private function saveTogglDataCache($data)
    {
        $cacheFile = getenv('alfred_workflow_data') . '/toggl_cache.json';
        file_put_contents($cacheFile, json_encode($data));
    }

    /**
     * @param  $description
     * @param  $projectId
     * @param  $taskId
     * @return mixed
     */
    private function startHarvestTimer($description, $projectId, $taskId)
    {
        $harvestId = $this->harvest->startTimer($description, $projectId, $taskId);
        $this->message = $this->harvest->getLastMessage();

        return $harvestId;
    }

    /**
     * @param  $description
     * @param  $projectId
     * @param  $tagNames
     * @return mixed
     */
    private function startTogglTimer($description, $projectId, $tagNames)
    {
        $togglId = $this->toggl->startTimer($description, $projectId, $tagNames);
        $this->message = $this->toggl->getLastMessage();

        return $togglId;
    }

    /**
     * @return mixed
     */
    private function stopHarvestTimer()
    {
        $harvestId = $this->config->get('workflow', 'timer_harvest_id');

        $res = $this->harvest->stopTimer($harvestId);
        $this->message = $this->harvest->getLastMessage();

        return $res;
    }

    /**
     * @return mixed
     */
    private function stopTogglTimer()
    {
        $togglId = $this->config->get('workflow', 'timer_toggl_id');

        $res = $this->toggl->stopTimer($togglId);
        $this->message = $this->toggl->getLastMessage();

        return $res;
    }

    /**
     * @return mixed
     */
    private function syncTogglOnlineDataToLocalCache()
    {
        $data = $this->toggl->getOnlineData();

        $this->message = $this->toggl->getLastMessage();

        if (empty($data) === false) {
            $this->saveTogglDataCache($data);
        }

        return $this->message;
    }
}
