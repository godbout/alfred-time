<?php

namespace Godbout\Alfred\Time;

use MorningTrain\TogglApi\TogglApi;

class Toggl
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

    private function extractFromData($needle)
    {
        $data = $this->getData();

        if (! isset($data->$needle)) {
            return [];
        }

        return array_column($data->$needle, 'name', 'id');
    }

    private function getData()
    {
        if (is_null($this->data)) {
            return $this->client->getMe(true);
        }

        return $this->data;
    }

    public function __toString()
    {
        return 'toggl';
    }
}
