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
        return $this->timerAction('delete', 'delete/' . $timerId);
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

        return $this->timerAction('stop', 'timer/' . $timerId);
    }

    /**
     * @param  $timerId
     * @return mixed
     */
    private function isTimerRunning($timerId)
    {
        $data = $this->timerAction('timer_running', 'show/' . $timerId);

        return isset($data['timer_started_at']);
    }

    /**
     * @param  string  $action
     * @param  string  $apiUri
     * @return mixed
     */
    private function timerAction($action, $apiUri, array $options = [])
    {
        $returnDataFor = ['start', 'timer_running'];
        $methods = [
            'start'         => 'post',
            'stop'          => 'get',
            'delete'        => 'delete',
            'timer_running' => 'get',
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
