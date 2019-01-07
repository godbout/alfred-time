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
    public function __construct(Config $config, Service $toggl, Service $harvest)
    {
        $this->config = $config;
        $this->toggl = $toggl;
        $this->harvest = $harvest;
    }

    /**
     * @param  array     $results
     * @param  $action
     * @return mixed
     */
    public function getNotification(array $results = [], $action = null)
    {
        $notification = '';

        if (empty($results) || empty($action)) {
            return '';
        }

        foreach ($results as $service => $status) {
            $notification .= $this->getNotificationForService($service, $action, $status);
        }

        return $notification;
    }

    /**
     * @param $service
     * @param null       $action
     * @param null       $success
     */
    public function getNotificationForService($service = null, $action = null, $success = null)
    {
        if (empty($success) === true) {
            return '- ' . ucfirst($service) . ': cannot ' . $action . ' []' . "\r\n";
        }

        return '- ' . ucfirst($service) . ': ' . $action . "\r\n";
    }

    /**
     * This method shouldn't be in that class but rather
     * in the services classes, but those classes don't have
     * access to the cache, so, I'd rather sleep than creating
     * a new Cache class and refactoring everything again
     * @param $service
     * @param $projectId
     */
    public function getProjectName($service, $projectId)
    {
        return $this->$service->getProjectName($projectId, $this->getServiceDataCache($service));
    }

    /**
     * @return mixed
     */
    public function getProjects()
    {
        return $this->getItems('projects');
    }

    /**
     * @return mixed
     */
    public function getRecentTimers()
    {
        $timers = [];

        foreach ($this->config->activatedServices() as $service) {
            $timers[$service] = $this->getRecentServiceTimers($service);
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
        return $this->getItems('tags');
    }

    /**
     * @return mixed
     */
    public function syncOnlineDataToLocalCache()
    {
        $message = '';

        foreach ($this->config->activatedServices() as $service) {
            $message .= $this->syncServiceOnlineDataToLocalCache($service);
        }

        return $message;
    }

    /**
     * @param  $needle
     * @return mixed
     */
    private function getItems($needle)
    {
        $items = [];
        $services = [];

        foreach ($this->config->activatedServices() as $service) {
            $services[$service] = call_user_func_array(
                [$this->$service, 'get' . ucfirst($needle)],
                [$this->getServiceDataCache($service)]
            );
        }

        foreach ($services as $serviceName => $serviceItems) {
            foreach ($serviceItems as $serviceItem) {
                $items[$serviceItem['name']][$serviceName] = $serviceItem['id'];
            }
        }

        return $items;
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

        $this->saveServiceDataCache($service, $data);

        if (empty($data) === true) {
            return $this->getNotificationForService($service, 'cache', false);
        }

        return $this->getNotificationForService($service, 'cached', true);
    }
}
