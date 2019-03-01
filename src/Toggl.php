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

        return $this->getProjectsFromData($data->projects);
    }

    private function getProjectsFromData($data)
    {
        $projects = [];

        foreach ($data as $project) {
            $projects[] = $project->name;
        }

        var_dump($projects);die;


        return $projects;
    }

    public function __toString()
    {
        return 'toggl';
    }
}
