<?php

namespace Godbout\Alfred\Time\Services;

use Exception;
use Carbon\Carbon;
use Carbon\CarbonInterval;
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

    public function pastTimers()
    {
        try {
            $pastTimers = [];

            $togglTimers = $this->client->getTimeEntriesInRange(Carbon::today(), Carbon::today()->subDays(30));

            return $this->convertToPastTimers($togglTimers);
        } catch (Exception $e) {
            return [];
        }
    }

    public function startTimer()
    {
        try {
            $timer = $this->client->startTimeEntry([
                'description' => getenv('timer_description'),
                'pid' => getenv('timer_project_id'),
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

    public function runningTimer()
    {
        $timer = $this->client->getRunningTimeEntry();

        return $timer->id ?? false;
    }

    public function continueTimer($timerId)
    {
        /**
         * Timer attributes are stored in env variables
         * gathered in startTimer.
         */
        return $this->startTimer();
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

    protected function convertToPastTimers($togglTimers)
    {
        $projects = $this->projects();

        return array_reverse(
            array_map(function ($togglTimer) use ($projects) {
                return $this->buildPastTimerObject($togglTimer, $projects);
            }, $togglTimers)
        );
    }

    protected function buildPastTimerObject($togglTimer, $projects)
    {
        $pastTimer['id'] = $togglTimer->id;
        $pastTimer['description'] = $togglTimer->description ?? '';
        $pastTimer['duration'] = CarbonInterval::seconds($togglTimer->duration)->cascade()->format('%H:%I:%S');

        if (isset($togglTimer->pid)) {
            $pastTimer['project_id'] = $togglTimer->pid;
            $pastTimer['project_name'] = $projects[$togglTimer->pid];
        }

        if (isset($togglTimer->tags)) {
            $pastTimer['tags'] = implode(', ', (array) $togglTimer->tags);
        }

        return (object) $pastTimer;
    }
}
