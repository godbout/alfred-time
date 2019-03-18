<?php

namespace Godbout\Alfred\Time;

use Valsplat\Harvest\Connection;
use Valsplat\Harvest\Harvest as HarvestApi;
use Valsplat\Harvest\Exceptions\ApiException;

class Harvest extends TimerService
{
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

            /**
             * iTodo
             *
             * - project id and task id mandatory
             * need to send them from test
             */

            $timer->save();

            if (! isset($timer->id)) {
                return false;
            }
        } catch (ApiException $e) {
            var_dump($e->getMessage());
            var_dump($timer);die;
            return false;
        }

        return $timer->id;
    }

    public function runningTimer()
    {
    }
}
