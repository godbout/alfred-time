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
    private $currentImplementation = [
        'start'        => ['toggl', 'harvest'],
        'stop'         => ['toggl', 'harvest'],
        'delete'       => ['toggl'],
        'get_projects' => ['toggl', 'harvest'],
        'get_tags'     => ['toggl', 'harvest'],
        'get_timers'   => ['toggl'],
        'sync_data'    => ['toggl', 'harvest'],
    ];

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
            if ($this->isServiceActive($service) === true) {
                array_push($activatedServices, $service);
            }
        }

        return $activatedServices;
    }

    public function generateDefaultConfigurationFile()
    {
        $this->config = [
            'timer' => [
                'primary_service'   => 'toggl',
                'is_running'  => false,
                'toggl_id'    => null,
                'harvest_id'  => null,
                'description' => '',
            ],
            'toggl'    => [
                'is_active'          => true,
                'api_token'          => '',
                'default_project_id' => '',
                'default_tags'       => '',
            ],
            'harvest'  => [

                'is_active'          => true,
                'domain'             => '',
                'api_token'          => '',
                'default_project_id' => '',
                'default_task_id'    => '',
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
     * @return mixed
     */
    public function getPrimaryService()
    {
        return $this->get('timer', 'primary_service');
    }

    /**
     * @return mixed
     */
    public function getTimerDescription()
    {
        return $this->get('timer', 'description');
    }

    /**
     * @return boolean
     */
    public function hasTimerRunning()
    {
        return $this->get('timer', 'is_running') === true;
    }

    /**
     * @param  string  $feature
     * @return mixed
     */
    public function implementedServicesForFeature($feature = null)
    {
        $services = [];

        if (isset($this->currentImplementation[$feature]) === true) {
            $services = $this->currentImplementation[$feature];
        }

        return $services;
    }

    /**
     * @return boolean
     */
    public function isConfigured()
    {
        return $this->config !== null;
    }

    /**
     * @param  $service
     * @return mixed
     */
    public function isServiceActive($service)
    {
        return $this->get($service, 'is_active');
    }

    /**
     * @return mixed
     */
    public function servicesToUndo()
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
