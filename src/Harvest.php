<?php

namespace Godbout\Alfred\Time;

use Valsplat\Harvest\Connection;
use Valsplat\Harvest\Harvest as HarvestApi;
use Valsplat\Harvest\Exceptions\ApiException;

class Harvest extends TimerService
{
    public $allowsEmptyProject = false;

    public $allowsEmptyTag = false;

    private $client;


    public function __construct($accountId, $apiToken)
    {
        $connection = new Connection();
        $connection->setAccountId($accountId);
        $connection->setAccessToken($apiToken);

        $this->client = new HarvestApi($connection);
    }

    public function projects()
    {
        return $this->items('project');
    }

    public function tags()
    {
        return $this->items('task');
    }

    protected function items($items = '')
    {
        try {
            return array_column($this->client->$items()->list(), 'name', 'id');
        } catch (ApiException $e) {
            return [];
        }
    }

    public function startTimer()
    {
        try {
            $timer = $this->client->timeEntry();

            $timer->notes = getenv('timer_description');
            $timer->project_id = getenv('timer_project');
            $timer->task_id = getenv('timer_tag');
            $timer->spent_date = date('Y-m-d');

            $timer->save();

            if (! isset($timer->id)) {
                return false;
            }
        } catch (ApiException $e) {
            return false;
        }

        return $timer->id;
    }

    public function runningTimer()
    {
        /**
         * The API is supposed to return only timers that are running
         * but it seems to be buggy. Returns all of them started
         * with newest on top.
         */
        $timer = $this->client->timeEntry()->list(['is_running' => true])[0];

        return $timer->is_running ? $timer->id : false;
    }

    public function stopCurrentTimer()
    {
        if ($timerId = $this->runningTimer()) {
            $timer = $this->client->timeEntry()->get($timerId);

            $timer->is_running = false;
            $res = $timer->save();

            var_dump($res);die;

            if (! isset($timer->id)) {
                throw new \Exception("Can't stop current running timer.", 1);

                return false;
            }

            return true;
        }

        return false;
    }
}
