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
     * @param $filename
     */
    public function __construct($filename = null)
    {

        if ($filename !== null) {
            $this->load($filename);
        }
    }

    public function generateDefaultConfigurationFile()
    {
        $this->config = [
            'workflow' => [
                'is_timer_running'  => false,
                'timer_toggl_id'    => null,
                'timer_harvest_id'  => null,
                'timer_description' => '',
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
            $res = $this->config;
        } elseif ($param === null) {
            $res = $this->config[$section];
        } else {
            $res = $this->config[$section][$param];
        }

        return $res;
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
