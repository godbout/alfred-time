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
    private $client = null;
    private $code = 0;
    private $message = '';
    private $data = [];

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

        if ($this->sendApiCall('post', 'time_entries/start', ['json' => $item]) === true) {
            if ($this->lastApiCall('success') === true) {
                $this->setMessage('timer started');
                $togglId = $this->data['data']['id'];
            } else {
                $this->setMessage('cannot start timer!');
            }
        }

        return $togglId;
    }

    public function stopTimer($timerId = null)
    {
        $res = false;

        if ($this->sendApiCall('put', 'time_entries/' . $timerId . '/stop') === true) {
            if ($this->lastApiCall('success') === true) {
                $this->setMessage('timer stopped');
                $res = true;
            } else {
                $this->setMessage('could not stop timer!');
            }
        }

        return $res;
    }

    public function deleteTimer($timerId = null)
    {
        $res = false;

        if ($this->sendApiCall('delete', 'time_entries/' . $timerId) === true) {
            if ($this->lastApiCall('success') === true) {
                $this->setMessage('timer deleted');
                $res = true;
            } else {
                $this->setMessage('could not delete timer!');
            }
        }

        return $res;
    }

    public function getRecentTimers()
    {
        $timers = [];

        if ($this->sendApiCall('get', 'time_entries') === true) {
            if ($this->lastApiCall('success') === true) {
                $timers = $this->data;
            }
        }

        return array_reverse($timers);
    }

    public function getOnlineData()
    {
        $data = [];

        if ($this->sendApiCall('get', 'me?with_related_data=true') === true) {
            if ($this->lastApiCall('success') === true) {
                $this->setMessage('data cached');
                $data = $this->data;
            } else {
                $this->setMessage('cannot get online data!');
            }
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

    private function sendApiCall($method, $uri = '', array $options = [])
    {
        $res = true;

        try {
            $response = $this->client->request(strtoupper($method), $uri, $options);
            $this->code = $response->getStatusCode();
            $this->data = json_decode($response->getBody(), true);
        } catch (ConnectException $e) {
            $this->setMessage('cannot connect to api!');
            $res = false;
        } catch (ClientException $e) {
            $this->setMessage($e->getResponse()->getBody());
        }

        return $res;
    }

    private function lastApiCall($status = '')
    {
        $res = false;

        switch ($status) {
            case 'success':
                if ($this->code >= 200 || $this->code <= 299) {
                    $res = true;
                }
                break;
        }

        return $res;
    }
}
