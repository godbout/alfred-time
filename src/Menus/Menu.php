<?php

namespace Godbout\Alfred\Time\Menus;

abstract class Menu
{
    abstract public static function content(): array;

    public static function userInput()
    {
        global $argv;

        return trim($argv[1] ?? '');
    }
}
