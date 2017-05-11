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
                'Content-type'  => 'application/json',
                'Accept'        => 'application/json',
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
        $res = $this->timerAction('delete', 'time_entries/' . $timerId);

        if ($res === true) {
            $this->setMessage('time deleted');
        } else {
            $this->setMessage('could not delete timer!' . $this->message);
        }

        return $res;
    }

    /**
     * @return mixed
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

        $data = $this->timerAction('get_online_data', 'me?with_related_data=true');

        if (empty($data) === false) {
            $this->setMessage('data cached');
        } else {
            $this->setMessage('cannot get online data! [' . $this->message . ']');
        }

        return $data;
    }

    public function getProjects()
    {
        # code...
    }

    public function getRecentTimers()
    {
        return array_reverse($this->timerAction('get_recent_timers', 'time_entries'));
    }

    /**
     * @param  $description
     * @param  $projectId
     * @param  $tagNames
     * @return mixed
     */
    public function startTimer($description, $projectId, $tagNames)
    {
        $togglId = null;

        $item = [
            'time_entry' => [
                'description'  => $description,
                'pid'          => $projectId,
                'tags'         => explode(', ', $tagNames),
                'created_with' => 'Alfred Time Workflow',
            ],
        ];

        $data = $this->timerAction('start', 'time_entries/start', ['json' => $item]);

        if (isset($data['data']['id']) === true) {
            $this->setMessage('timer started');
            $togglId = $data['data']['id'];
        } else {
            $this->setMessage('could not start timer! [' . $this->message . ']');
        }

        return $togglId;
    }

    /**
     * @param  $timerId
     * @return mixed
     */
    public function stopTimer($timerId = null)
    {
        $res = $this->timerAction('stop', 'time_entries/' . $timerId . '/stop');

        if ($res === true) {
            $this->setMessage('timer stopped');
        } else {
            $this->setMessage('could not stop timer! [' . $this->message . ']');
        }

        return $res;
    }

    /**
     * @param $message
     */
    private function setMessage($message = null)
    {
        $this->message = '- Toggl: ' . $message;
    }

    /**
     * @param  $action
     * @param  $apiUri
     * @return mixed
     */
    private function timerAction($action, $apiUri, array $options = [])
    {
        $res = false;
        $method = '';
        $returnDataFor = ['start', 'get_recent_timers', 'get_online_data'];

        switch ($action) {
            case 'delete':
                $method = 'delete';
                break;
            case 'stop':
                $method = 'put';
                break;
            case 'start':
                $method = 'post';
                break;
            case 'get_recent_timers':
            // no break
            case 'get_online_data':
                $method = 'get';
                break;
        }

        if ($this->serviceApiCall->send($method, $apiUri, $options) === true) {
            $res = $this->serviceApiCall->last('success');

            if (in_array($action, $returnDataFor) === true) {
                $res = $this->serviceApiCall->getData();
            }
        } else {
            $this->message = $this->serviceApiCall->getMessage();
        }

        return $res;
    }
}
