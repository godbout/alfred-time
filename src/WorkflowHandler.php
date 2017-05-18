<?php

namespace AlfredTime;

use AlfredTime\Config;

class WorkflowHandler
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
     * @return mixed
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
     * @param  $service
     * @return mixed
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
