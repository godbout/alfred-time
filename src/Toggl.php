<?php

namespace AlfredTime;

/**
 *
 */
class Toggl extends Service
{
    /**
     * @var string
     */
    protected $apiBaseUrl = 'https://www.toggl.com/api/v8/';

    /**
     * @var array
     */
    protected $methods = [
        'start' => 'post',
        'stop' => 'put',
        'delete' => 'delete',
        'get_recent_timers' => 'get',
        'get_online_data' => 'get',
    ];

    /**
     * @param $apiToken
     */
    public function __construct($apiToken = null)
    {
        parent::__construct($this->apiBaseUrl, base64_encode($apiToken . ':api_token'));
    }

    /**
     * @param $timerId
     */
    public function apiDeleteUrl($timerId)
    {
        return 'time_entries/' . $timerId;
    }

    public function apiStartUrl()
    {
        return 'time_entries/start';
    }

    /**
     * @param $timerId
     */
    public function apiStopUrl($timerId)
    {
        return 'time_entries/' . $timerId . '/stop';
    }

    /**
     * @param $description
     * @param $projectId
     * @param $tagData
     */
    public function generateTimer($description, $projectId, $tagData)
    {
        return [
            'time_entry' => [
                'description' => $description,
                'pid' => $projectId,
                'tags' => explode(', ', $tagData),
                'created_with' => 'Alfred Time Workflow',
            ],
        ];
    }

    /**
     * @return mixed
     */
    public function getOnlineData()
    {
        $data = [];

        $togglData = $this->timerAction('get_online_data', 'me?with_related_data=true');

        if ($togglData !== false) {
            $data = $togglData;
        }

        return $data;
    }

    /**
     * @param  $projectId
     * @return mixed
     */
    public function getProjectName($projectId, array $data = [])
    {
        $projectName = '';
        $projects = $this->getProjects($data);

        foreach ($projects as $project) {
            if ($project['id'] === $projectId) {
                $projectName = $project['name'];

                break;
            }
        }

        return $projectName;
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
     * @return mixed
     */
    public function getRecentTimers()
    {
        $timers = [];

        foreach ($this->timerAction('get_recent_timers', 'time_entries') as $timeEntry) {
            $timers[] = [
                'id' => $timeEntry['id'],
                'description' => $timeEntry['description'],
                'project_id' => $timeEntry['pid'],
                'project_name' => $timeEntry['pid'],
                'tags' => empty($timeEntry['tags']) ? '' : implode(', ', $timeEntry['tags']),
                'duration' => $timeEntry['duration'],
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
        return $this->getItems('tags', $data);
    }

    /**
     * @param $action
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
        $returnDataFor = ['start', 'get_recent_timers', 'get_online_data'];

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

        if (isset($haystack['data'][$needle]) === false) {
            return [];
        }

        /**
         * To only show projects that are currently active
         * The Toggl API is slightly weird on that
         */
        $items = array_filter($haystack['data'][$needle], function ($item) {
            return isset($item['server_deleted_at']) === false;
        });

        return array_map(function ($item) use ($needle) {
            return [
                    'name' => $item['name'],
                    'id' => ($needle === 'tags') ? $item['name'] : $item['id'],
                ];
        }, $items);
    }
}
