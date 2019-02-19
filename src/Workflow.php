<?php

namespace Godbout\Time;

use Godbout\Alfred\ScriptFilter;

class Workflow
{
    private $workflowDataFolder;

    private $configFile;

    private $config = null;

    private $scriptFilter = null;

    private static $instance = null;

    protected function __construct()
    {
        $this->workflowDataFolder = getenv('alfred_workflow_data');
        $this->configFile = $this->workflowDataFolder . '/config.json';
        self::createWorkflowDataFolderAndConfigFileIfNeeded();
        $this->config = Config::load($this->configFile);
    }

    public static function output()
    {
        self::getInstance();

        ScriptFilter::create();

        foreach (self::getCurrentMenuClass()::content() as $item) {
            ScriptFilter::add($item);
        }

        return ScriptFilter::output();
    }

    private function createWorkflowDataFolderAndConfigFileIfNeeded()
    {
        if (! file_exists($this->workflowDataFolder)) {
            mkdir($this->workflowDataFolder);
        }

        if (! file_exists($this->configFile)) {
            file_put_contents(
                $this->configFile,
                json_encode(self::getDefaultConfig(), JSON_PRETTY_PRINT)
            );
        }
    }

    private static function getDefaultConfig()
    {
        return [
            'timer' => [
                'primary_service' => 'toggl',
                'is_running' => false,
                'toggl_id' => null,
                'harvest_id' => null,
                'description' => '',
            ],
            'toggl' => [
                'is_active' => true,
                'api_token' => '',
            ],
            'harvest' => [
                'is_active' => false,
                'domain' => '',
                'api_token' => '',
            ],
        ];
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public static function getConfigFile()
    {
        return self::getInstance()->configFile;
    }

    public static function getConfig()
    {
        return self::getInstance()->config;
    }

    public static function destroy()
    {
        self::$instance = null;
    }

    private static function getCurrentMenuClass()
    {
        $action = getenv('action');

        return __NAMESPACE__ . '\\Menus\\' . self::getMenuClassName($action);
    }

    private static function getMenuClassName($action)
    {
        return str_replace('_', '', ucwords($action === false ? 'none' : $action, '_'));
    }

    private function __clone()
    {
    }
}
