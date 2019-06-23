<?php

namespace Godbout\Alfred\Time\Services;

use GuzzleHttp\Client;

class Everhour extends TimerService
{
    private $client;

    private $data = null;


    public function __construct($apiToken)
    {
        $this->client = new Client([
            'base_uri' => 'https://api.everhour.com/',
            'headers' => [
                'X-Api-Key' => $apiToken
            ]
        ]);
    }

    public function projects()
    {
        try {
            $response = $this->client->get('projects');
            $projects = json_decode($response->getBody()->getContents());

            return array_column($projects, 'name', 'id');
        } catch (Exception $e) {
            return [];
        }
    }

    public function tags()
    {
        try {
            $projectId = getenv('timer_project_id');
            $response = $this->client->get("projects/$projectId/tasks");
            $tasks = json_decode($response->getBody()->getContents());

            array_walk($tasks, function ($task) use (&$tags) {
                $tags[$task->id] = $task->name;
            });

            return $tags;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function pastTimers()
    {
        return [];
    }

    public function startTimer()
    {
        try {
            $response = $this->client->post('timers', [
                'json' => [
                    'task' => getenv('timer_tag_id'),
                    'comment' => getenv('timer_description')
                ]
            ]);

            $timer = json_decode($response->getBody()->getContents());

            if (! isset($timer->status) || $timer->status !== 'active') {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    public function stopCurrentTimer()
    {
        try {
            $response = $this->client->delete('timers/current');

            $timer = json_decode($response->getBody()->getContents());

            if (! isset($timer->taskTime) || $timer->status !== 'stopped') {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    public function runningTimer()
    {
        try {
            $response = $this->client->get('timers/current');

            $timer = json_decode($response->getBody()->getContents());

            if (! isset($timer->duration) || $timer->status !== 'active') {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    public function continueTimer($timerId)
    {
        return false;
    }

    public function deleteTimer($timerId)
    {
        return false;
    }
}
