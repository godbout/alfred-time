<?php

namespace AlfredTime;

use AlfredTime\ServiceApiCall;

/**
 *
 */
class Harvest
{
    /**
     * @var string
     */
    private $message = '';

    /**
     * @var mixed
     */
    private $serviceApiCall = null;

    /**
     * @param $domain
     * @param null      $apiToken
     */
    public function __construct($domain = null, $apiToken = null)
    {
        $this->serviceApiCall = new ServiceApiCall([
            'base_uri' => 'https://' . $domain . '.harvestapp.com/',
            'headers'  => [
                'Authorization' => 'Basic ' . $apiToken,
            ],
        ]);
    }

    /**
     * @param  $timerId
     * @return mixed
     */
    public function deleteTimer($timerId = null)
    {
        return $this->timerAction('delete', 'daily/delete/' . $timerId);
    }

    /**
     * @return string
     */
    public function getLastMessage()
    {
        return $this->message;
    }

    /**
     * @return mixed
     */
    public function getOnlineData()
    {
        $data = [];
        $data['projects'] = $this->timerAction('get_projects', 'projects');
        $data['tasks'] = $this->timerAction('get_tags', 'tasks');

        return $data;
    }

    /**
     * @param  $data
     * @return mixed
     */
    public function getProjects($data)
    {
        return $this->getItems('projects', $data);
    }

    /**
     * @param  $data
     * @return mixed
     */
    public function getTags($data)
    {
        return $this->getItems('tasks', $data);
    }

    /**
     * @param  $description
     * @param  $projectId
     * @param  $taskId
     * @return mixed
     */
    public function startTimer($description, $projectId, $taskId)
    {
        $harvestId = null;

        $item = [
            'notes'      => $description,
            'project_id' => $projectId,
            'task_id'    => $taskId,
        ];

        $data = $this->timerAction('start', 'daily/add/', ['json' => $item]);

        if (isset($data['id']) === true) {
            $harvestId = $data['id'];
        }

        return $harvestId;
    }

    /**
     * @param  $timerId
     * @return mixed
     */
    public function stopTimer($timerId = null)
    {
        if ($this->isTimerRunning($timerId) === false) {
            return false;
        }

        return $this->timerAction('stop', 'daily/timer/' . $timerId);
    }

    /**
     * @param  $needle
     * @param  array     $haystack
     * @return mixed
     */
    private function getItems($needle, array $haystack = [])
    {
        $items = [];

        if (isset($haystack[$needle]) === false) {
            return [];
        }

        foreach ($haystack[$needle] as $item) {
            $items[] = [
                'name' => $item[key($item)]['name'],
                'id'   => $item[key($item)]['id'],
            ];
        }

        return $items;
    }

    /**
     * @param  $timerId
     * @return boolean
     */
    private function isTimerRunning($timerId)
    {
        $data = $this->timerAction('timer_running', 'daily/show/' . $timerId);

        return isset($data['timer_started_at']);
    }

    /**
     * @param  string  $action
     * @param  string  $apiUri
     * @return mixed
     */
    private function timerAction($action, $apiUri, array $options = [])
    {
        $returnDataFor = [
            'start',
            'timer_running',
            'get_projects',
            'get_tags',
        ];
        $methods = [
            'start'         => 'post',
            'stop'          => 'get',
            'delete'        => 'delete',
            'timer_running' => 'get',
            'get_projects'  => 'get',
            'get_tags'      => 'get',
            '',
        ];

        $method = isset($methods[$action]) ? $methods[$action] : '';

        if ($this->serviceApiCall->send($method, $apiUri, $options) === false) {
            $this->message = $this->serviceApiCall->getMessage();

            return false;
        }

        if (in_array($action, $returnDataFor) === true) {
            return $this->serviceApiCall->getData();
        }

        return $this->serviceApiCall->last('success');
    }
}
