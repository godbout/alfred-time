<?php

namespace AlfredTime;

use AlfredTime\Toggl;
use AlfredTime\Config;
use AlfredTime\Harvest;

class Timer
{
    /**
     * @var mixed
     */
    private $config;

    /**
     * @var mixed
     */
    private $harvest;

    /**
     * @var mixed
     */
    private $toggl;

    /**
     * @param Config $config
     */
    public function __construct(Config $config = null)
    {
        $this->config = $config;
        $this->harvest = new Harvest($this->config->get('harvest', 'domain'), $this->config->get('harvest', 'api_token'));
        $this->toggl = new Toggl($this->config->get('toggl', 'api_token'));
    }

    /**
     * @param  $service
     * @param  $timerId
     * @return boolean
     */
    public function deleteServiceTimer($service, $timerId)
    {
        if ($this->$service->deleteTimer($timerId) === false) {
            return false;
        }

        if ($timerId === $this->getProperty($service . '_id')) {
            $this->updateProperty($service . '_id', null);
        }

        return true;
    }

    /**
     * @param  $timerId
     * @return string
     */
    public function delete(array $timerData = [])
    {
        $message = '';
        $oneTimerDeleted = false;

        foreach ($timerData as $service => $id) {
            $res = $this->deleteServiceTimer($service, $id);
            $message .= $this->setNotificationForService($service, 'delete', $res);
            $oneTimerDeleted = $oneTimerDeleted || $res;
        }

        if ($oneTimerDeleted === true) {
            $this->updateProperty('is_running', false);
        }

        return $message;
    }

    public function getDescription()
    {
        return $this->getProperty('description');
    }

    /**
     * @return mixed
     */
    public function getPrimaryService()
    {
        return $this->getProperty('primary_service');
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
        $services = [];

        foreach ($this->config->implementedServicesForFeature('get_projects') as $service) {
            if ($this->config->isServiceActive($service) === true) {
                $services[$service] = $this->$service->getProjects($this->getServiceDataCache($service));
            }
        }

        foreach ($services as $serviceName => $serviceProjects) {
            foreach ($serviceProjects as $serviceProject) {
                $projects[$serviceProject['name']][$serviceName . '_id'] = $serviceProject['id'];
            }
        }

        return $projects;
    }

    /**
     * @return mixed
     */
    public function getRecentTimers()
    {
        $timers = [];

        foreach ($this->config->implementedServicesForFeature('get_timers') as $service) {
            if ($this->config->isServiceActive($service) === true) {
                $timers = array_merge($timers, $this->getRecentServiceTimers($service));
            }
        }

        return $timers;
    }

    /**
     * @param  $service
     * @return mixed
     */
    public function getServiceDataCache($service)
    {
        $cacheFile = getenv('alfred_workflow_data') . '/' . $service . '_cache.json';

        if (file_exists($cacheFile) === false) {
            $this->syncServiceOnlineDataToLocalCache($service);
        }

        return json_decode(file_get_contents($cacheFile), true);
    }

    /**
     * @return mixed
     */
    public function getTags()
    {
        $tags = [];
        $services = [];

        foreach ($this->config->implementedServicesForFeature('get_tags') as $service) {
            if ($this->config->isServiceActive($service) === true) {
                $services[$service] = $this->$service->getTags($this->getServiceDataCache($service));
            }
        }

        foreach ($services as $serviceName => $serviceTags) {
            foreach ($serviceTags as $serviceTag) {
                $tags[$serviceTag['name']][$serviceName . '_id'] = $serviceTag['id'];
            }
        }

        return $tags;
    }

    /**
     * @return mixed
     */
    public function isRunning()
    {
        return $this->getProperty('is_running');
    }

    /**
     * @param $service
     * @param null       $action
     * @param null       $success
     */
    public function setNotificationForService($service = null, $action = null, $success = null)
    {
        if (empty($success) === true) {
            return '- ' . ucfirst($service) . ': cannot ' . $action . ' [' . $this->$service->getLastMessage() . ']' . "\r\n";
        }

        return '- ' . ucfirst($service) . ': ' . $action . "\r\n";
    }

    /**
     * @param  $description
     * @param  $projectsData
     * @param  $tagData
     * @return string
     */
    public function start($description = '', array $projectData = [], array $tagData = [], $specificService = null)
    {
        $message = '';
        $oneServiceStarted = false;

        $servicesToRun = ($specificService === null) ? $this->config->implementedServicesForFeature('start') : [$specificService];

        /**
         * When starting a new timer, all the services timer IDs have to be put to null
         * so that when the user uses the UNDO feature, it doesn't delete old previous
         * other services timers. The timer IDs are used for the UNDO feature and
         * should then contain the IDs of the last starts through the workflow, not
         * through each individual sefrvice
         */
        if (empty($servicesToRun) === true) {
            return '';
        }

        foreach ($this->config->activatedServices() as $service) {
            $this->updateProperty($service . '_id', null);
        }

        foreach ($servicesToRun as $service) {
            $timerId = $this->$service->startTimer($description, $projectData[$service . '_id'], $tagData[$service . '_id']);
            $this->updateProperty($service . '_id', $timerId);
            $message .= $this->setNotificationForService($service, 'start', $timerId);
            $oneServiceStarted = $oneServiceStarted || ($timerId !== null);
        }

        if ($oneServiceStarted === true) {
            $this->updateProperty('description', $description);
            $this->updateProperty('is_running', true);
        }

        return $message;
    }

    /**
     * @return string
     */
    public function stop()
    {
        $message = '';
        $oneServiceStopped = false;

        foreach ($this->config->runningServices() as $service) {
            $timerId = $this->getProperty($service . '_id');

            $res = $this->$service->stopTimer($timerId);
            $message .= $this->setNotificationForService($service, 'stop', $res);
            $oneServiceStopped = $oneServiceStopped || $res;
        }

        if ($oneServiceStopped === true) {
            $this->updateProperty('is_running', false);
        }

        return $message;
    }

    /**
     * @return string
     */
    public function syncOnlineDataToLocalCache()
    {
        $message = '';

        foreach ($this->config->implementedServicesForFeature('sync_data') as $service) {
            if ($this->config->isServiceActive($service) === true) {
                $message .= $this->syncServiceOnlineDataToLocalCache($service);
            }
        }

        return $message;
    }

    /**
     * @return string
     */
    public function undo()
    {
        $timerData = [];

        foreach ($this->config->runningServices() as $service) {
            $timerData[$service] = $this->getProperty($service .'_id');
        }

        return $this->delete($timerData);
    }

    /**
     * @param $name
     * @param $value
     */
    public function updateProperty($name, $value)
    {
        $this->config->update('timer', $name, $value);
    }

    /**
     * @param  $name
     * @return mixed
     */
    private function getProperty($name)
    {
        return $this->config->get('timer', $name);
    }

    /**
     * @return mixed
     */
    private function getRecentServiceTimers($service)
    {
        return $this->$service->getRecentTimers();
    }

    /**
     * @param $data
     * @param string  $service
     */
    private function saveServiceDataCache($service, $data)
    {
        $cacheFile = getenv('alfred_workflow_data') . '/' . $service . '_cache.json';
        file_put_contents($cacheFile, json_encode($data));
    }

    /**
     * @param  string   $service
     * @return string
     */
    private function syncServiceOnlineDataToLocalCache($service)
    {
        $data = $this->$service->getOnlineData();

        if (empty($data) === true) {
            return $this->setNotificationForService($service, 'data', false);
        }

        $this->saveServiceDataCache($service, $data);

        return $this->setNotificationForService($service, 'data', true);
    }
}
