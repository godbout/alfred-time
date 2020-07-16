<?php

namespace Godbout\Alfred\Time\Services;

use Carbon\CarbonInterval;
use GuzzleHttp\Client;

class Clockify extends TimerService
{
    private $client;

    private $data = null;


    public function __construct($apiToken)
    {
        $this->client = new Client([
            'base_uri' => 'https://api.clockify.me/api/v1',
            'headers' => [
                'Api-Key' => $apiToken
            ]
        ]);
    }

    protected function workspaces()
    {
        return [];
    }

    public function projects()
    {
        return [];
    }

    public function tags()
    {
        return [];
    }

    public function pastTimers()
    {
        return [];
    }

    public function startTimer()
    {
        return false;
    }

    public function stopCurrentTimer()
    {
        return false;
    }

    public function runningTimer()
    {
        return false;
    }

    public function continueTimer($timerId = null)
    {
        return false;
    }

    public function deleteTimer($timerId)
    {
        return false;
    }
}
