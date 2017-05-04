<?php

require __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;

/**
 *
 */
class Toggl
{
    private $client;
    private $message;

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

        $this->setMessage('Just init');
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

        try {
            $response = $this->client->post('time_entries/start', [
                'json' => $item,
            ]);

            $code = $response->getStatusCode();

            if ($code < 200 || $code > 299) {
                $this->setMessage('cannot start timer!');
            } else {
                $data = json_decode($response->getBody(), true);
                $togglId = $data['data']['id'];
                $this->setMessage('timer started');
            }
        } catch (ConnectException $e) {
            $this->setMessage('cannot connect to api!');
        }

        return $togglId;
    }

    public function stopTimer($timerId = null)
    {
        $res = false;

        try {
            $response = $this->client->put('time_entries/' . $timerId . '/stop');

            if ($response->getStatusCode() !== 200) {
                $this->setMessage('could not stop timer!');
            } else {
                $this->setMessage('timer stopped');
                $res = true;
            }
        } catch (ConnectException $e) {
            $this->setMessage('cannot connect to api!');
        } catch (ClientException $e) {
            $this->setMessage($e->getResponse()->getBody());
        }

        return $res;
    }

    public function deleteTimer($timerId = null)
    {
        $res = false;

        try {
            $response = $this->client->delete('time_entries/' . $timerId);

            if ($response->getStatusCode() !== 200) {
                $this->setMessage('could not delete timer!');
            } else {
                $this->setMessage('timer deleted');
                $res = true;
            }
        } catch (ConnectException $e) {
            $this->setMessage('cannot connect to api!');
        } catch (ClientException $e) {
            $this->setMessage($e->getResponse()->getBody());
        }

        return $res;
    }

    public function getRecentTimers()
    {
        $timers = [];

        try {
            $response = $this->client->get('time_entries');

            if ($response->getStatusCode() === 200) {
                $timers = json_decode($response->getBody(), true);
            }
        } catch (ConnectException $e) {
            $this->setMessage('cannot connect to api!');
        } catch (ClientException $e) {
            $this->setMessage($e->getResponse()->getBody());
        }

        return array_reverse($timers);
    }

    public function getOnlineData()
    {
        $data = [];

        try {
            $response = $this->client->get('me?with_related_data=true');

            $code = $response->getStatusCode();

            if ($code < 200 || $code > 299) {
                $this->setMessage('cannot get online data!');
            } else {
                $data = json_decode($response->getBody(), true);
                $this->setMessage('data cached');
            }
        } catch (ConnectException $e) {
            $this->setMessage('cannot connect to api!');
        } catch (ClientException $e) {
            $this->setMessage($e->getResponse()->getBody());
        }

        return $data;
    }

    public function getLastMessage()
    {
        return $this->message;
    }

    private function setMessage($message = null)
    {
        $this->message = '- Toggl: ' . $message;
    }
}
