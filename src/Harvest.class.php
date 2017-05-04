<?php

require __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;

/**
 *
 */
class Harvest
{
    private $client = null;
    private $code = 0;
    private $message = '';
    private $data = null;

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
    }

    public function startTimer($description, $projectId, $taskId)
    {
        $harvestId = null;

        $item = [
            'notes' => $description,
            'project_id' => $projectId,
            'task_id' => $taskId,
        ];

        if ($this->sendApiCall('post', 'add', ['json' => $item]) === true) {
            if ($this->lastApiCall('success') === true) {
                $this->setMessage('timer started');
                $harvestId = $this->data['id'];
            } else {
                $this->setMessage('cannot start timer!');
            }
        }

        return $harvestId;
    }

    public function stopTimer($timerId = null)
    {
        $res = false;

        if ($this->isTimerRunning($timerId) === true) {
            try {
                $response = $this->client->get('timer/' . $timerId);

                if ($response->getStatusCode() !== 200) {
                    $this->setMessage('could not stop timer!');
                } else {
                    $this->setMessage('timer stopped');
                    $res = true;
                }
            } catch (ConnectException $e) {
                $this->setMessage('cannot connect to api!');

            } catch (ClientException $e) {
                $this->setMessage($e->getRequest()->getBody());
            }
        } else {
            $this->setMessage('timer was not running');
        }

        return $res;
    }

    public function deleteTimer($timerId = null)
    {
        $res = false;

        if ($this->sendApiCall('delete', 'delete/' . $timerId) === true) {
            if ($this->lastApiCall('success') === true) {
                $this->setMessage('timer deleted');
                $res = true;
            } else {
                $this->setMessage('could not delete timer!');
            }
        }

        return $res;
    }

    private function isTimerRunning($timerId)
    {
        $res = false;

        if ($this->sendApiCall('get', 'show/' . $timerId) === true) {
            if ($this->lastApiCall('success') === true) {
                if (isset($this->data['timer_started_at']) === true) {
                    $res = true;
                }
            }
        }

        return $res;
    }

    public function getLastMessage()
    {
        return $this->message;
    }

    private function setMessage($message = null)
    {
        $this->message = '- Harvest: ' . $message;
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
                if ($this->code === 200 || $this->code === 201) {
                    $res = true;
                }
                break;
        }

        return $res;
    }
}
