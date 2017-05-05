<?php

require __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;

/**
 * ServiceApiCall
 */
class ServiceApiCall
{
    private $client = null;
    private $code = '';
    private $message = '';
    private $data = null;

    public function __construct(array $config = [])
    {
        $this->client = new Client($config);
    }

    public function send($method, $uri = '', array $options = [])
    {
        $res = true;

        try {
            $response = $this->client->request(strtoupper($method), $uri, $options);
            $this->code = $response->getStatusCode();
            $this->data = json_decode($response->getBody(), true);
        } catch (ConnectException $e) {
            $this->message = 'cannot connect to api!';
            $res = false;
        } catch (ClientException $e) {
            $this->message = $e->getResponse()->getBody();
        }

        return $res;
    }

    public function last($status = '')
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

    public function getCode()
    {
        return $this->code;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getData()
    {
        return $this->data;
    }

}
