<?php

namespace AlfredTime;

abstract class Service
{
    /**
     * @var mixed
     */
    protected $serviceApiCall = null;

    /**
     * @param  $timerId
     * @return mixed
     */
    public function deleteTimer($timerId)
    {
        return $this->serviceApiCall->send(
            $this->methodForAction('delete'),
            $this->apiDeleteUrl($timerId)
        );
    }

    /**
     * @param  $descrition
     * @param  $projectId
     * @param  $tagData
     * @param mixed $description
     * @return mixed
     */
    public function startTimer($description, $projectId, $tagData)
    {
        $timerId = null;

        $item = $this->generateTimer($description, $projectId, $tagData);

        $data = $this->serviceApiCall->send(
            $this->methodForAction('start'),
            $this->apiStartUrl(),
            ['json' => $item],
            true
        );

        if (isset($data['id']) === true) {
            $timerId = $data['id'];
        }

        if (isset($data['data']['id']) === true) {
            $timerId = $data['data']['id'];
        }

        return $timerId;
    }

    /**
     * @param  $timerId
     * @return mixed
     */
    public function stopTimer($timerId)
    {
        return $this->serviceApiCall->send(
            $this->methodForAction('stop'),
            $this->apiStopUrl($timerId)
        );
    }

    /**
     * @param $baseUri
     * @param $apiToken
     * @param mixed $credentials
     */
    protected function __construct($baseUri, $credentials)
    {
        $this->serviceApiCall = new ServiceApiCall([
            'base_uri' => $baseUri,
            'headers' => [
                'Authorization' => 'Basic ' . $credentials,
            ],
        ]);
    }

    /**
     * @param $timerId
     */
    abstract protected function apiDeleteUrl($timerId);

    abstract protected function methodForAction($action);

    /**
     * @param $timerId
     */
    abstract protected function apiStopUrl($timerId);

    abstract protected function getOnlineData();

    /**
     * @param $data
     */
    abstract protected function getProjects($data);

    abstract protected function getRecentTimers();

    /**
     * @param $data
     */
    abstract protected function getTags($data);
}
