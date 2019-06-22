<?php

namespace Godbout\Alfred\Time;

use Godbout\Alfred\Workflow\Config;
use Godbout\Alfred\Time\Services\Toggl;
use Godbout\Alfred\Time\Services\Harvest;
use Godbout\Alfred\Workflow\ScriptFilter;
use Godbout\Alfred\Time\Services\Everhour;

class Workflow
{
    const SERVICES = [
        'toggl',
        'harvest',
        'everhour'
    ];

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

    public static function currentMenu()
    {
        self::getCurrentMenuClass()::scriptFilter();

        return self::getInstance()->scriptFilter->output();
    }

    public static function do()
    {
        $action = getenv('timer_action');

        if ($timerId = getenv('timer_id')) {
            return Timer::$action($timerId);
        }

        if (method_exists(Timer::class, $action)) {
            return Timer::$action();
        }

        return true;
    }

    public static function notify($result = false)
    {
        $action = getenv('timer_action');

        $service = ucfirst(self::serviceEnabled());

        if ($result === false) {
            return "Oops... $service cannot $action.";
        }

        return "$service $action!";
    }

    private static function getDefaultConfig()
    {
        return include __DIR__ . '/../config/default.php';
    }

    public static function getConfig()
    {
        return self::getInstance()->config;
    }

    public static function enableService($service = '')
    {
        return self::getInstance()->serviceStatus($service, true);
    }

    public static function disableService($service = '')
    {
        return self::getInstance()->serviceStatus($service, false);
    }

    public static function services()
    {
        return self::SERVICES;
    }

    protected function serviceStatus($service, $status = false)
    {
        self::getInstance()->disableAllServices();

        if (self::getInstance()->classExistsForService($service)) {
            Workflow::getConfig()->write("$service.is_active", $status);

            return true;
        }

        return false;
    }

    protected function classExistsForService($service = '')
    {
        return class_exists(__NAMESPACE__ . '\\Services\\' . ucfirst($service));
    }

    public static function serviceEnabled()
    {
        if (self::getInstance()->getConfig()->read('toggl.is_active')) {
            return new Toggl(
                Workflow::getConfig()->read('toggl.api_token')
            );
        }

        if (self::getInstance()->getConfig()->read('harvest.is_active')) {
            return new Harvest(
                Workflow::getConfig()->read('harvest.account_id'),
                Workflow::getConfig()->read('harvest.api_token')
            );
        }

        if (self::getInstance()->getConfig()->read('everhour.is_active')) {
            return new Everhour(
                Workflow::getConfig()->read('everhour.api_token')
            );
        }

        return null;
    }

    public static function disableAllServices()
    {
        foreach (self::SERVICES as $service) {
            Workflow::getConfig()->write("$service.is_active", false);
        }
    }

    private static function getCurrentMenuClass()
    {
        $args = explode('_', getenv('action'));

        if (in_array($args[0], self::SERVICES)) {
            $service = ucfirst($args[0]);
            $action = substr(getenv('action'), strlen($args[0]));

            return __NAMESPACE__ . "\\Menus\\$service\\" . self::getMenuClassName($action);
        }

        return __NAMESPACE__ . "\\Menus\\" . (self::getMenuClassName(getenv('action')) ?: 'Entrance');
    }

    private static function getMenuClassName($action)
    {
        return str_replace('_', '', ucwords($action === false ? 'entrance' : $action, '_'));
    }

    public static function destroy()
    {
        ScriptFilter::destroy();

        self::$instance = null;
    }

    private function __clone()
    {
    }
}
