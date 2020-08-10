<?php

namespace Godbout\Alfred\Time\Services;

use Carbon\Carbon;
use Godbout\Alfred\Time\Workflow;
use GuzzleHttp\Client;

class Clockify extends TimerService
{
    private $client;


    public function __construct($apiToken)
    {
        $this->client = new Client([
            'base_uri' => 'https://api.clockify.me/api/v1/',
            'headers' => [
                'content-type' => 'application/json',
                'X-Api-Key' => $apiToken
            ]
        ]);
    }

    public function projects()
    {
        try {
            $user = json_decode(
                $this->client->get("user")->getBody()->getContents()
            );

            Workflow::getConfig()->write('clockify.active_workspace_id', $user->activeWorkspace);

            $response = $this->client->get("workspaces/{$user->activeWorkspace}/projects?page-size=148");

            $projects = json_decode($response->getBody()->getContents());

            return array_column($projects, 'name', 'id');
        } catch (\Exception $e) {
            return [];
        }
    }

    public function tags()
    {
        try {
            $workspaceId = Workflow::getConfig()->read('clockify.active_workspace_id');

            $response = $this->client->get("workspaces/$workspaceId/tags");
            $tags = json_decode($response->getBody()->getContents());

            return array_column($tags, 'name', 'id');
        } catch (\Exception $e) {
            return [];
        }
    }

    public function startTimer()
    {
        try {
            $workspaceId = Workflow::getConfig()->read('clockify.active_workspace_id');

            $response = $this->client->post(
                "workspaces/$workspaceId/time-entries",
                $this->buildPayload()
            );

            $timer = json_decode($response->getBody()->getContents());

            if (! isset($timer->id)) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

        return $timer->id;
    }

    protected function buildPayload()
    {
        $payload = [
            'json' => [
                'start' => (new \DateTime())->format('Y-m-d\TH:i:s\Z'),
                'description' => getenv('timer_description'),
            ]
        ];

        if (getenv('timer_project_id') !== false) {
            $payload['json']['projectId'] = getenv('timer_project_id');
        }

        if (getenv('timer_tag_id') !== false) {
            $payload['json']['tagIds'] = [getenv('timer_tag_id')];
        }

        return $payload;
    }

    public function stopCurrentTimer()
    {
        $user = json_decode(
            $this->client->get("user")->getBody()->getContents()
        );

        if ($this->runningTimer()) {
            $response = $this->client->patch("workspaces/{$user->activeWorkspace}/user/{$user->id}/time-entries", [
                'json' => [
                    'end' => (new \DateTime())->format('Y-m-d\TH:i:s\Z'),
                ]
            ]) ;

            $timer = json_decode($response->getBody()->getContents());

            if (! isset($timer->timeInterval->end)) {
                throw new \Exception("Can't stop current running timer.", 1);
            }

            return true;
        }

        return false;
    }

    public function runningTimer()
    {
        try {
            $user = json_decode(
                $this->client->get("user")->getBody()->getContents()
            );

            $response = $this->client->get(
                "workspaces/{$user->activeWorkspace}/user/{$user->id}/time-entries?in-progress=true"
            );

            $timer = json_decode($response->getBody()->getContents());

            return $timer[0]->id ?? false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function pastTimers()
    {
        try {
            $user = json_decode(
                $this->client->get("user")->getBody()->getContents()
            );

            $response = $this->client->get("workspaces/{$user->activeWorkspace}/user/{$user->id}/time-entries", [
                'start' => Carbon::today(),
                'end' => Carbon::today()->subDays(30),
            ]);
            $clockifyTimers = json_decode($response->getBody()->getContents());

            return $this->convertToPastTimers($clockifyTimers);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function continueTimer($timerId = null)
    {
        /**
         * Timer attributes are stored in env variables
         * gathered in startTimer.
         */
        return $this->startTimer();
    }

    protected function convertToPastTimers($clockifyTimers)
    {
        $projects = $this->projects();
        $tags = $this->tags();

        return array_map(function ($clockifyTimer) use ($projects, $tags) {
            return $this->buildPastTimerObject($clockifyTimer, $projects, $tags);
        }, $clockifyTimers);
    }

    protected function buildPastTimerObject($clockifyTimer, $projects, $tags)
    {
        $pastTimer = [];

        $pastTimer['id'] = $clockifyTimer->id;
        $pastTimer['description'] = $clockifyTimer->description;

        if (isset($clockifyTimer->projectId)) {
            $pastTimer['project_id'] = $clockifyTimer->projectId;
            $pastTimer['project_name'] = $projects[$clockifyTimer->projectId];
        }

        if (isset($clockifyTimer->tagIds[0])) {
            $pastTimer['tag_id'] = $clockifyTimer->tagIds[0];
            $pastTimer['tags'] = $tags[$clockifyTimer->tagIds[0]];
        }

        $pastTimer['duration'] = $clockifyTimer->timeInterval->duration;

        return (object) $pastTimer;
    }
}
