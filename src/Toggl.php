<?php

namespace Godbout\Alfred\Time;

use Exception;
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

    public function startTimer()
    {
        try {
            $response = $this->client->startTimeEntry([
                'description' => getenv('timer_description'),
                'pid' => getenv('timer_project'),
                'tags' => getenv('timer_tag') ? [getenv('timer_tag')] : '',
                'created_with' => 'Alfred Time'
            ]);
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
