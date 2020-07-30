<?php

namespace Godbout\Alfred\Time\Services;

use Carbon\Carbon;
use GuzzleHttp\Client;

class Clockify extends TimerService
{
    private $client;

    private $data = null;


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

    public function workspaces()
    {
        try {
            $response = $this->client->get('workspaces');
            $workspaces = json_decode($response->getBody()->getContents());

            return array_column($workspaces, 'name', 'id');
        } catch (\Exception $e) {
            return [];
        }
    }

    public function projects()
    {
        try {
            $workspaceId = getenv('timer_workspace_id');

            $response = $this->client->get("workspaces/$workspaceId/projects");

            $projects = json_decode($response->getBody()->getContents());

            return array_column($projects, 'name', 'id');
        } catch (\Exception $e) {
            return [];
        }
    }

    public function tags()
    {
        try {
            $workspaceId = getenv('timer_workspace_id');

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
            $workspaceId = getenv('timer_workspace_id');

            $response = $this->client->post("workspaces/$workspaceId/time-entries", [
                'json' => [
                    'start' => (new \DateTime())->format('Y-m-d\TH:i:s\Z'),
                    'description' => getenv('timer_description'),
                    'projectId' => getenv('timer_project_id'),
                    'tagIds' => getenv('timer_tag_id') ? [getenv('timer_tag_id')] : [''],
                ]
            ]);

            $timer = json_decode($response->getBody()->getContents());

            if (! isset($timer->id)) {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }

        return $timer->id;
    }

    public function stopCurrentTimer()
    {
        $workspaceId = getenv('timer_workspace_id');
        $userId = getenv('timer_user_id');

        if ($timerId = $this->runningTimer()) {
            $response = $this->client->patch("workspaces/$workspaceId/user/$userId/time-entries", [
                'json' => [
                    'end' => (new \DateTime())->format('Y-m-d\TH:i:s\Z'),
                ]
            ]) ;

            $timer = json_decode($response->getBody()->getContents());

            if (! isset($timer->timeInterval->end)) {
                throw new Exception("Can't stop current running timer.", 1);

                return false;
            }

            return true;
        }

        return false;
    }

    public function runningTimer()
    {
        try {
            $workspaceId = getenv('timer_workspace_id');
            $userId = getenv('timer_user_id');

            $response = $this->client->get("workspaces/$workspaceId/user/$userId/time-entries?in-progress=true");

            $timer = json_decode($response->getBody()->getContents());

            return $timer[0]->id ?? false;
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    public function pastTimers()
    {
        try {
            $pastTimers = [];

            $workspaceId = getenv('timer_workspace_id');
            $userId = getenv('timer_user_id');

            $response = $this->client->get("workspaces/$workspaceId/user/$userId/time-entries", [
                'start' => Carbon::today(),
                'end' => Carbon::today()->subDays(30),
            ]);
            $clockifyTimers = json_decode($response->getBody()->getContents());

            return $this->convertToPastTimers($clockifyTimers);
        } catch (Exception $e) {
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

    public function deleteTimer($timerId)
    {
        return false;
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
