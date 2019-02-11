<?php

namespace AlfredTime;

/**
 *
 */
class Harvest extends Service
{
    /**
     * @var array
     */
    protected $methods = [
        'start' => 'post',
        'stop' => 'get',
        'delete' => 'delete',
        'timer_running' => 'get',
        'get_projects' => 'get',
        'get_tags' => 'get',
        'get_recent_timers' => 'get',
    ];

    /**
     * @param $domain
     * @param null      $apiToken
     */
    public function __construct($domain = null, $apiToken = null)
    {
        parent::__construct('https://' . $domain . '.harvestapp.com/', $apiToken);
    }

    /**
     * @param $timerId
     */
    public function apiDeleteUrl($timerId)
    {
        return 'daily/delete/' . $timerId;
    }

    public function apiStartUrl()
    {
        return 'daily/add/';
    }

    /**
     * @param $timerId
     */
    public function apiStopUrl($timerId)
    {
        return 'daily/timer/' . $timerId;
    }

    /**
     * @param  $description
     * @param  $projectId
     * @param  $taskId
     * @return mixed
     */
    public function generateTimer($description, $projectId, $taskId)
    {
        return [
            'notes' => $description,
            'project_id' => $projectId,
            'task_id' => $taskId,
        ];
    }

    /**
     * @return mixed
     */
    public function getOnlineData()
    {
        $data = [];

        $projects = $this->timerAction('get_projects', 'projects');

        if ($projects !== false) {
            $data['projects'] = $projects;
        }

        $tasks = $this->timerAction('get_tags', 'tasks');

        if ($tasks !== false) {
            $data['tasks'] = $tasks;
        }

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

    public function getRecentTimers()
    {
        $timers = [];

        foreach ($this->timerAction('get_recent_timers', 'daily')['day_entries'] as $dayEntry) {
            $timers[] = [
                'id' => $dayEntry['id'],
                'description' => $dayEntry['notes'],
                'project_id' => $dayEntry['project_id'],
                'project_name' => $dayEntry['project'],
                'tags' => $dayEntry['task'],
                'duration' => $dayEntry['hours'] * 60 * 60,
            ];
        }

        return array_reverse($timers);
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
     * @param  $action
     * @return mixed
     */
    protected function methodForAction($action)
    {
        if (isset($this->methods[$action]) === false) {
            return;
        }

        return $this->methods[$action];
    }

    /**
     * @param  string  $action
     * @param  string  $apiUri
     * @return mixed
     */
    protected function timerAction($action, $apiUri, array $options = [])
    {
        $returnDataFor = [
            'start',
            'timer_running',
            'get_projects',
            'get_tags',
            'get_recent_timers',
        ];

        $method = $this->methodForAction($action);

        return $this->serviceApiCall->send(
            $method,
            $apiUri,
            $options,
            in_array($action, $returnDataFor)
        );
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
                'id' => $item[key($item)]['id'],
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
}
