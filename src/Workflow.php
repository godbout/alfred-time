<?php

namespace Godbout\Alfred\Time;

use Godbout\Alfred\Workflow\Config;
use Godbout\Alfred\Workflow\ScriptFilter;

class Workflow
{
    private static $instance = null;

    private $config = null;

    private $scriptFilter = null;


    protected function __construct()
    {
        $this->config = Config::ifEmptyStartWith(self::getDefaultConfig());
        $this->scriptFilter = ScriptFilter::create();
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public static function output()
    {
        self::getInstance()->scriptFilter->create();

        foreach (self::getCurrentMenuClass()::content() as $item) {
            self::getInstance()->scriptFilter->add($item);
        }

        return self::getInstance()->scriptFilter->output();
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

    public static function getConfig()
    {
        return self::getInstance()->config;
    }

    public static function destroy()
    {
        ScriptFilter::destroy();

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
