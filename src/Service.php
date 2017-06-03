<?php

namespace AlfredTime;

use AlfredTime\ServiceApiCall;

abstract class Service
{
    /**
     * @var mixed
     */
    protected $serviceApiCall = null;

    /**
     * @param $baseUri
     * @param $apiToken
     */
    protected function __construct($baseUri, $credentials)
    {
        $this->serviceApiCall = new ServiceApiCall([
            'base_uri' => $baseUri,
            'headers'  => [
                'Authorization' => 'Basic ' . $credentials,
            ],
        ]);
    }
}
