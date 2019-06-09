<?php

namespace Godbout\Alfred\Time\Menus;

abstract class Menu
{
    abstract public static function scriptFilter();

    public static function userInput()
    {
        global $argv;

        return trim($argv[1] ?? '');
    }
}
