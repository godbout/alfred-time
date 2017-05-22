<?php

namespace AlfredTime;

/**
 * Config
 */
class Config
{
    /**
     * @var mixed
     */
    private $config = [];

    /**
     * @var array
     */
    private $services = [
        'toggl',
        'harvest',
    ];

    /**
     * @param $filename
     */
    public function __construct($filename = null)
    {
        if ($filename !== null) {
            $this->load($filename);
        }
    }

    /**
     * @return mixed
     */
    public function activatedServices()
    {
        $activatedServices = [];

        foreach ($this->services as $service) {
            if ($this->get($service, 'is_active') === true) {
                array_push($activatedServices, $service);
            }
        }

        return $activatedServices;
    }

    public function generateDefaultConfigurationFile()
    {
        $this->config = [
            'timer'   => [
                'primary_service' => 'toggl',
                'is_running'      => false,
                'toggl_id'        => null,
                'harvest_id'      => null,
                'description'     => '',
            ],
            'toggl'   => [
                'is_active' => true,
                'api_token' => '',
            ],
            'harvest' => [

                'is_active' => true,
                'domain'    => '',
                'api_token' => '',
            ],
        ];

        $this->save();
    }

    /**
     * @param  $section
     * @param  null       $param
     * @return mixed
     */
    public function get($section = null, $param = null)
    {
        if ($section === null) {
            return $this->config;
        } elseif ($param === null) {
            return $this->config[$section];
        }

        return $this->config[$section][$param];
    }

    /**
     * @return boolean
     */
    public function isConfigured()
    {
        return empty($this->config) === false;
    }

    /**
     * @return mixed
     */
    public function runningServices()
    {
        $services = [];

        foreach ($this->activatedServices() as $service) {
            if ($this->get('timer', $service . '_id') !== null) {
                array_push($services, $service);
            }
        }

        return $services;
    }

    /**
     * @param $section
     * @param $param
     * @param $value
     */
    public function update($section, $param, $value)
    {
        $this->config[$section][$param] = $value;
        $this->save();
    }

    /**
     * @return mixed
     */
    private function load($filename)
    {
        if (file_exists($filename)) {
            $this->config = json_decode(file_get_contents($filename), true);
        }
    }

    private function save()
    {
        $workflowDir = getenv('alfred_workflow_data');
        $configFile = $workflowDir . '/config.json';

        if (file_exists($workflowDir) === false) {
            mkdir($workflowDir);
        }

        file_put_contents($configFile, json_encode($this->config, JSON_PRETTY_PRINT));
    }
}
