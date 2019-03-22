<?php

namespace Godbout\Alfred\Time;

use Required\Harvest\Client;
use Required\Harvest\Exception\NotFoundException;
use Required\Harvest\Exception\AuthenticationException;
use Required\Harvest\Exception\ValidationFailedException;

class Harvest extends TimerService
{
    public $allowsEmptyProject = false;

    public $allowsEmptyTag = false;

    private $client;


    public function __construct($accountId, $apiToken)
    {
        $this->client = new Client();

        $this->client->authenticate($accountId, $apiToken);
    }

    public function projects()
    {
        try {
            return array_column($this->client->projects()->all(['']), 'name', 'id');
        } catch (AuthenticationException $e) {
            return [];
        }
    }

    public function tags()
    {
        try {
            $taskAssignments = $this->client->projects()->taskAssignments()->all((int) getenv('timer_project'), ['is_active' => true]);

            array_walk($taskAssignments, function ($taskAssignment) use (&$tags) {
                $tags[$taskAssignment['task']['id']] = $taskAssignment['task']['name'];
            });

            return $tags;
        } catch (AuthenticationException $e) {
            return [];
        }
    }

    public function startTimer()
    {
        try {
            $timer = $this->client->timeEntries()->create([
                'notes' => getenv('timer_description'),
                'project_id' => (int) getenv('timer_project'),
                'task_id' => (int) getenv('timer_tag_id'),
                'spent_date' => date('Y-m-d')
            ]);

            if (! isset($timer['id'])) {
                return false;
            }
        } catch (ValidationFailedException $e) {
            return false;
        }

        return $timer['id'];
    }

    public function stopCurrentTimer()
    {
        if ($timerId = $this->runningTimer()) {
            $timer = $this->client->timeEntries()->stop($timerId);

            if (! isset($timer['id'])) {
                throw new \Exception("Can't stop current running timer.", 1);

                return false;
            }

            return true;
        }

        return false;
    }

    public function runningTimer()
    {
        $timer = $this->client->timeEntries()->all(['is_running' => true]);

        return $timer[0]['id'] ?? false;
    }

    public function continueTimer($timerId)
    {
        $timer = $this->client->timeEntries()->restart($timerId);

        return $timer['id'] ?? false;
    }

    public function deleteTimer($timerId)
    {
        try {
            $this->client->timeEntries()->remove($timerId);
        } catch (NotFoundException $e) {
            return false;
        }

        return true;
    }
}
