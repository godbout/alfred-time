<?php

namespace AlfredTime;

use AlfredTime\ServiceApiCall;

/**
 *
 */
class Toggl
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
     * @param $apiToken
     */
    public function __construct($apiToken = null)
    {
        $this->serviceApiCall = new ServiceApiCall([
            'base_uri' => 'https://www.toggl.com/api/v8/',
            'headers'  => [
                'Authorization' => 'Basic ' . base64_encode($apiToken . ':api_token'),
            ],
        ]);
    }

    /**
     * @param  $timerId
     * @return mixed
     */
    public function deleteTimer($timerId = null)
    {
        return $this->timerAction('delete', 'time_entries/' . $timerId);
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
        return $this->timerAction('get_online_data', 'me?with_related_data=true');
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
        return array_reverse($this->timerAction('get_recent_timers', 'time_entries'));
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
     * @param  $description
     * @param  $projectId
     * @param  $tagNames
     * @return mixed
     */
    public function startTimer($description, $projectId, $tagData)
    {
        $togglId = null;
        $item = [
            'time_entry' => [
                'description'  => $description,
                'pid'          => $projectId,
                'tags'         => explode(', ', $tagData),
                'created_with' => 'Alfred Time Workflow',
            ],
        ];

        $data = $this->timerAction('start', 'time_entries/start', ['json' => $item]);

        if (isset($data['data']['id']) === true) {
            $togglId = $data['data']['id'];
        }

        return $togglId;
    }

    /**
     * @param  $timerId
     * @return mixed
     */
    public function stopTimer($timerId = null)
    {
        return $this->timerAction('stop', 'time_entries/' . $timerId . '/stop');
    }

    /**
     * @param  string  $action
     * @param  string  $apiUri
     * @return mixed
     */
    public function timerAction($action, $apiUri, array $options = [])
    {
        $returnDataFor = ['start', 'get_recent_timers', 'get_online_data'];
        $methods = [
            'start'             => 'post',
            'stop'              => 'put',
            'delete'            => 'delete',
            'get_recent_timers' => 'get',
            'get_online_data'   => 'get',
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
        foreach ($haystack['data'][$needle] as $key => $item) {
            if (isset($item['server_deleted_at']) === true) {
                unset($haystack['data'][$needle][$key]);
            }

            $items[] = [
                'name' => $item['name'],
                'id'   => $item['id'],
            ];
        }

        return $items;
    }
}
