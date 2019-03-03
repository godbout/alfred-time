<?php

namespace Godbout\Alfred\Time;

use MorningTrain\TogglApi\TogglApi;

class Toggl
{
    private $client;

    public function __construct($apiToken)
    {
        $this->client = new TogglApi($apiToken);
    }

    public function projects()
    {
        $data = $this->client->getMe(true);

        if (! isset($data->projects)) {
            return [];
        }

        return $this->getProjectsFromData($data->projects);
    }

    private function getProjectsFromData($data)
    {
        return array_column($data, 'name', 'id');
    }

    public function __toString()
    {
        return 'toggl';
    }
}
