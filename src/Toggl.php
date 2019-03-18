<?php

namespace Godbout\Alfred\Time;

use Exception;
use MorningTrain\TogglApi\TogglApi;

class Toggl extends TimerService
{
    private $client;

    private $data = null;


    public function __construct($apiToken)
    {
        $this->client = new TogglApi($apiToken);
    }

    public function projects()
    {
        return $this->extractFromData('projects');
    }

    public function tags()
    {
        return $this->extractFromData('tags');
    }

    public function startTimer()
    {
        try {
            $timer = $this->client->startTimeEntry([
                'description' => getenv('timer_description'),
                'pid' => getenv('timer_project'),
                'tags' => getenv('timer_tag') ? [getenv('timer_tag')] : '',
                'created_with' => 'Alfred Time'
            ]);

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
        if ($timerId = $this->runningTimer()) {
            $response = $this->client->stopTimeEntry($timerId);

            if (! isset($response->id)) {
                throw new Exception("Can't stop current running timer.", 1);

                return false;
            }

            return true;
        }

        return false;
    }

    public function deleteTimer($timerId)
    {
        try {
            $this->client->deleteTimeEntry($timerId);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    public function runningTimer()
    {
        $timer = $this->client->getRunningTimeEntry();

        return $timer->id ?? false;
    }

    private function extractFromData($needle)
    {
        $data = $this->getData();

        if (! isset($data->$needle)) {
            return [];
        }

        $nonDeletedData = $this->filterOutServerwiseDeletedItemsFromData($data->$needle);

        return array_column($nonDeletedData, 'name', 'id');
    }

    private function getData()
    {
        if (is_null($this->data)) {
            return $this->client->getMe(true);
        }

        return $this->data;
    }

    private function filterOutServerwiseDeletedItemsFromData($items = [])
    {
        return array_filter($items, function ($item) {
            return ! isset($item->server_deleted_at);
        });
    }
}
