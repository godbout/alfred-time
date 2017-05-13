<?php

namespace AlfredTime;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;

/**
 * ServiceApiCall
 */
class ServiceApiCall
{
    /**
     * @var mixed
     */
    private $client = null;

    /**
     * @var int
     */
    private $code = 0;

    /**
     * @var mixed
     */
    private $data = null;

    /**
     * @var string
     */
    private $message = '';

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->client = new Client($config);
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param  $status
     * @return mixed
     */
    public function last($status = '')
    {
        $res = false;

        switch ($status) {
            case 'success':
                if ($this->code >= 200 && $this->code <= 299) {
                    $res = true;
                }

                break;
        }

        return $res;
    }

    /**
     * @param  $method
     * @param  $uri
     * @param  array     $options
     * @return mixed
     */
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
            $res = false;
            $this->code = $e->getResponse()->getStatusCode();
            $this->message = $e->getResponse()->getBody();
        }

        return $res;
    }
}
