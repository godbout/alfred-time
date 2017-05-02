<?php

require 'vendor/autoload.php';

use GuzzleHttp\Client;

/**
 *
 */
class Toggl
{
    private $client;
    private $message;
    private $apiToken;

    public function __construct($apiToken = null)
    {
        $this->client = new Client([
            'base_uri' => 'https://www.toggl.com/api/v8/',
            'headers' => [
                'Content-type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($apiToken . ':api_token'),
            ],
        ]);
        $this->message = '';
    }

    public function startTimer($description, $projectId, $tagNames)
    {
        $togglId = null;

        $item = [
            'time_entry' => [
                'description' => $description,
                'pid' => $projectId,
                'tags' => explode(', ', $tagNames),
                'created_with' => 'Alfred Time Workflow',
            ],
        ];

        $response = $this->client->post('time_entries/start', [
            'json' => $item,
        ]);

        $code = $response->getStatusCode();

        if ($code < 200 || $code > 299) {
            $this->message = '- Cannot start Toggl timer!';
        } else {
            $data = json_decode($response->getBody(), true);
            $togglId = $data['data']['id'];
            $this->message = '- Toggl timer started';
        }

        return $togglId;
    }

    public function stopTimer($timerId = null)
    {
        $res = false;

        $response = $this->client->put('time_entries/' . $timerId . '/stop');

        if ($response->getStatusCode() !== 200) {
            $this->message = '- Could not stop Toggl timer!';
        } else {
            $this->message = '- Toggl timer stopped';
            $res = true;
        }

        return $res;
    }

    public function getLastMessage()
    {
        return $this->message;
    }

    public function deleteTimer($timerId = null)
    {
        $res = false;

        $response = $this->client->delete('time_entries/' . $timerId);

        if ($response->getStatusCode() !== 200) {
            $this->message = '- Could not delete Toggl timer!';
        } else {
            $this->message = '- Toggl timer deleted';
            $res = true;
        }

        return $res;
    }

    public function getRecentTimers()
    {
        $timers = [];

        $response = $this->client->get('time_entries');

        if ($response->getStatusCode() === 200) {
            $timers = json_decode($response->getBody(), true);
        }

        return array_reverse($timers);
    }

    public function getOnlineData()
    {
        $data = [];

        $response = $this->client->get('me?with_related_data=true');

        $code = $response->getStatusCode();

        if ($code < 200 || $code > 299) {
            $this->message = '- Cannot get Toggl online data!';
        } else {
            $data = json_decode($response->getBody(), true);
            $this->message = '- Toggl data cached';
        }

        return $data;
    }
}
