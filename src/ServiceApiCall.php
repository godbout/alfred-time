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
     * @var string
     */
    private $message = '';

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $config = array_merge_recursive($config, [
            'headers' => [
                'Content-type' => 'application/json',
                'Accept'       => 'application/json',
            ]]);

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
        switch ($status) {
            case 'success':
                if ($this->code >= 200 && $this->code <= 299) {
                    return true;
                }

                break;
        }

        return false;
    }

    /**
     * @param  $method
     * @param  $uri
     * @param  array     $options
     * @return mixed
     */
    public function send($method, $uri = '', array $options = [], $returnData = false)
    {
        try {
            $response = $this->client->request(strtoupper($method), $uri, $options);
            $this->code = $response->getStatusCode();

            if ($returnData === false) {
                return true;
            }

            return json_decode($response->getBody(), true);
        } catch (ConnectException $e) {
            $this->message = 'cannot connect to api!';

            return false;
        } catch (ClientException $e) {
            $this->code = $e->getResponse()->getStatusCode();
            $this->message = $e->getResponse()->getBody();

            return false;
        }
    }
}
