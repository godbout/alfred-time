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
        $this->serviceApiCall = new serviceApiCall([
            'base_uri' => 'https://' . $domain . '.harvestapp.com/daily/',
            'headers'  => [
                'Content-type'  => 'application/json',
                'Accept'        => 'application/json',
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
        $res = $this->timerAction('delete', 'delete/' . $timerId);

        if ($res === true) {
            $this->setMessage('timer deleted');
        } else {
            $this->setMessage('could not delete timer! [' . $this->message . ']');
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

        $data = $this->timerAction('start', 'add', ['json' => $item]);

        if (isset($data['id']) === true) {
            $this->setMessage('timer started');
            $harvestId = $data['id'];
        } else {
            $this->setMessage('could not start timer! [' . $this->message . ']');
        }

        return $harvestId;
    }

    /**
     * @param  $timerId
     * @return mixed
     */
    public function stopTimer($timerId = null)
    {
        $res = false;

        if ($this->isTimerRunning($timerId) === true) {
            $res = $this->timerAction('stop', 'timer/' . $timerId);

            if ($res === true) {
                $this->setMessage('timer stopped');
            } else {
                $this->setMessage('could not stop timer! [' . $this->message . ']');
            }
        } else {
            $this->setMessage('timer was not running');
        }

        return $res;
    }

    /**
     * @param  $timerId
     * @return mixed
     */
    private function isTimerRunning($timerId)
    {
        $data = $this->timerAction('timer_running', 'show/' . $timerId);
        $res = isset($data['timer_started_at']);

        return $res;
    }

    /**
     * @param $message
     */
    private function setMessage($message = null)
    {
        $this->message = '- Harvest: ' . $message;
    }

    /**
     * @param  string  $action
     * @param  string  $apiUri
     * @return mixed
     */
    private function timerAction($action, $apiUri, array $options = [])
    {
        $res = false;
        $returnDataFor = ['start', 'timer_running'];
        $methods = [
            'start'         => 'post',
            'stop'          => 'get',
            'delete'        => 'delete',
            'timer_running' => 'get',
        ];
        $method = isset($methods[$action]) ? $methods[$action] : '';

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
