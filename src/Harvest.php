<?php

namespace Godbout\Alfred\Time;

use Valsplat\Harvest\Connection;
use Valsplat\Harvest\Exceptions\ApiException;
use Valsplat\Harvest\Harvest as HarvestApi;

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

    public function runningTimer()
    {
    }
}
