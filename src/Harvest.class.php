<?php

require_once 'ServiceApiCall.class.php';

/**
 *
 */
class Harvest
{
    private $serviceApiCall = null;
    private $message = '';

    public function __construct($domain = null, $apiToken = null)
    {
        $this->serviceApiCall = new serviceApiCall([
            'base_uri' => 'https://' . $domain . '.harvestapp.com/daily/',
            'headers' => [
                'Content-type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . $apiToken,
            ],
        ]);
    }

    public function startTimer($description, $projectId, $taskId)
    {
        $harvestId = null;

        $item = [
            'notes' => $description,
            'project_id' => $projectId,
            'task_id' => $taskId,
        ];

        if ($this->serviceApiCall->send('post', 'add', ['json' => $item]) === true) {
            if ($this->serviceApiCall->last('success') === true) {
                $this->setMessage('timer started');
                $harvestId = $this->serviceApiCall->getData()['id'];
            } else {
                $this->setMessage('cannot start timer!');
            }
        } else {
            $this->setMessage($this->serviceApiCall->getMessage());
        }

        return $harvestId;
    }

    public function stopTimer($timerId = null)
    {
        $res = false;

        if ($this->isTimerRunning($timerId) === true) {
            if ($this->serviceApiCall->send('get', 'timer/' . $timerId) === true) {
                if ($this->serviceApiCall->last('success') === true) {
                    $this->setMessage('timer stopped');
                    $res = true;
                } else {
                    $this->setMessage('could not stop timer!');
                }
            } else {
                $this->setMessage($this->serviceApiCall->getMessage());
            }
        } else {
            $this->setMessage('timer was not running');
        }

        return $res;
    }

    public function deleteTimer($timerId = null)
    {
        $res = false;

        if ($this->serviceApiCall->send('delete', 'delete/' . $timerId) === true) {
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

    private function isTimerRunning($timerId)
    {
        $res = false;

        if ($this->serviceApiCall->send('get', 'show/' . $timerId) === true) {
            if ($this->serviceApiCall->last('success') === true) {
                if (isset($this->serviceApiCall->getData()['timer_started_at']) === true) {
                    $res = true;
                }
            }
        } else {
            $this->setMessage($this->serviceApiCall->getMessage());
        }

        return $res;
    }

    public function getLastMessage()
    {
        return $this->message;
    }

    private function setMessage($message = null)
    {
        $this->message = '- Harvest: ' . $message;
    }

}
