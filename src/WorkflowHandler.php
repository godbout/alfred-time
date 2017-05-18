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
        $this->harvest = new Harvest(
            $this->config->get('harvest', 'domain'),
            $this->config->get('harvest', 'api_token')
        );
        $this->toggl = new Toggl($this->config->get('toggl', 'api_token'));
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
                $items[$serviceItem['name']][$serviceName . '_id'] = $serviceItem['id'];
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

        if (empty($data) === true) {
            return $this->getNotificationForService($service, 'data', false);
        }

        $this->saveServiceDataCache($service, $data);

        return $this->getNotificationForService($service, 'data', true);
    }
}
