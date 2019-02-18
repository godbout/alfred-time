<?php

namespace Godbout\Time;

use Godbout\Alfred\ScriptFilter;

class Workflow
{
    public static function output()
    {
        ScriptFilter::create();

        $class = self::getCurrentMenuClass();
        $class::content();

        return ScriptFilter::output();
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
}
