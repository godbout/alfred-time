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

        $this->setMessage('Just init');
    }

    public function startTimer($description, $projectId, $taskId)
    {
        $harvestId = null;

        $item = [
            'notes' => $description,
            'project_id' => $projectId,
            'task_id' => $taskId,
        ];

        try {
            $response = $this->client->post('add', [
                'json' => $item,
            ]);

            if ($response->getStatusCode() !== 201) {
                $this->setMessage('cannot start timer!');
            } else {
                $data = json_decode($response->getBody(), true);
                $harvestId = $data['id'];
                $this->setMessage('timer started');
            }
        } catch (ConnectException $e) {
            $this->setMessage('cannot connect to api!');
        } catch (ClientException $e) {
            $this->setMessage($e->getResponse()->getBody());
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

        try {
        $response = $this->client->delete('delete/' . $timerId);

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

    private function isTimerRunning($timerId)
    {
        $res = false;

        try {
            $response = $this->client->get('show/' . $timerId);

            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getBody(), true);
                if (isset($data['timer_started_at']) === true) {
                    $res = true;
                }
            }
        } catch (ConnectException $e) {
            $this->setMessage('cannot connect to api!');
        } catch (ClientException $e) {
            $this->setMessage($e->getResponse()->getBody());
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
}
