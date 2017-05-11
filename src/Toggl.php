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
        $res = false;

        if ($this->serviceApiCall->send('delete', 'time_entries/' . $timerId) === true) {
            if ($this->serviceApiCall->last('success') === true) {
                $this->setMessage('timer deleted');
                $res = true;
            } else {
                $this->setMessage('could not delete timer!');
            }
        } else {
            $this->setMessage($this->serviceApiCall->getMessage());
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

        if ($this->serviceApiCall->send('get', 'me?with_related_data=true') === true) {
            if ($this->serviceApiCall->last('success') === true) {
                $this->setMessage('data cached');
                $data = $this->serviceApiCall->getData();
            } else {
                $this->setMessage('cannot get online data!');
            }
        } else {
            $this->setMessage($this->serviceApiCall->getMessage());
        }

        return $data;
    }

    public function getRecentTimers()
    {
        $timers = [];

        if ($this->serviceApiCall->send('get', 'time_entries') === true) {
            if ($this->serviceApiCall->last('success') === true) {
                $timers = $this->serviceApiCall->getData();
            }
        } else {
            $this->setMessage($this->serviceApiCall->getMessage());
        }

        return array_reverse($timers);
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

        if ($this->serviceApiCall->send('post', 'time_entries/start', ['json' => $item]) === true) {
            if ($this->serviceApiCall->last('success') === true) {
                $this->setMessage('timer started');
                $togglId = $this->serviceApiCall->getData()['data']['id'];
            } else {
                $this->setMessage('cannot start timer!');
            }
        } else {
            $this->setMessage($this->serviceApiCall->getMessage());
        }

        return $togglId;
    }

    /**
     * @param  $timerId
     * @return mixed
     */
    public function stopTimer($timerId = null)
    {
        $res = false;

        if ($this->serviceApiCall->send('put', 'time_entries/' . $timerId . '/stop') === true) {
            if ($this->serviceApiCall->last('success') === true) {
                $this->setMessage('timer stopped');
                $res = true;
            } else {
                $this->setMessage('could not stop timer!');
            }
        } else {
            $this->setMessage($this->serviceApiCall->getMessage());
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

    public function getProjects()
    {
        # code...
    }
}
