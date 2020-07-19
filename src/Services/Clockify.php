<?php

namespace Godbout\Alfred\Time\Services;

use JDecool\Clockify\ClientBuilder;

class Clockify extends TimerService
{
    private $client;

    private $data = null;


    public function __construct($apiToken)
    {
        $this->client = (new ClientBuilder())->createClientV1($apiToken);
    }

    public function workspaces()
    {
        try {
            return $this->client->get('workspaces');
        } catch (\Exception $e) {
            return [];
        }
    }

    public function projects()
    {
        try {
            $workspaceId = getenv('timer_workspace_id');

            return $this->client->get("workspaces/$workspaceId/projects");
        } catch (\Exception $e) {
            return [];
        }
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
