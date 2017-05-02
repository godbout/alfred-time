<?php

require __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;

/**
 *
 */
class Harvest
{
    private $client;
    private $message;

    public function __construct($domain = null, $apiToken = null)
    {
        $this->client = new Client([
            'base_uri' => 'https://' . $domain . '.harvestapp.com/daily/',
            'headers' => [
                'Content-type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . $apiToken,
            ],
        ]);
        $this->message = '';
    }

    public function startTimer($description, $projectId, $taskId)
    {
        $harvestId = null;

        $item = [
            'notes' => $description,
            'project_id' => $projectId,
            'task_id' => $taskId,
        ];

        $response = $this->client->post('add', [
            'json' => $item,
        ]);

        if ($response->getStatusCode() !== 201) {
            $this->message = '- Cannot start Harvest timer!';
        } else {
            $data = json_decode($response->getBody(), true);
            $harvestId = $data['id'];
            $this->message = '- Harvest timer started';
        }

        return $harvestId;
    }

    public function stopTimer($timerId = null)
    {
        $res = false;

        if ($this->isTimerRunning($timerId) === true) {
            $response = $this->client->get('timer/' . $timerId);

            if ($response->getStatusCode() !== 200) {
                $this->message = '- Could not stop Harvest timer!';
            } else {
                $this->message = '- Harvest timer stopped';
                $res = true;
            }
        } else {
            $this->message = '- Harvest timer was not running';
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

        $response = $this->client->delete('delete/' . $timerId);

        if ($response->getStatusCode() !== 200) {
            $this->message = '- Could not delete Harvest timer!';
        } else {
            $this->message = '- Harvest timer deleted';
            $res = true;
        }

        return $res;
    }

    private function isTimerRunning($timerId)
    {
        $res = false;

        $response = $this->client->get('show/' . $timerId);

        if ($response->getStatusCode() === 200) {
            $data = json_decode($response->getBody(), true);
            if (isset($data['timer_started_at']) === true) {
                $res = true;
            }
        }

        return $res;
    }
}
