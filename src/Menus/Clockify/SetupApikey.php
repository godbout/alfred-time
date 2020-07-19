<?php

namespace Godbout\Alfred\Time\Menus\Clockify;

use Godbout\Alfred\Time\Menus\Menu;
use Godbout\Alfred\Workflow\Icon;
use Godbout\Alfred\Workflow\Item;
use Godbout\Alfred\Workflow\ScriptFilter;

class SetupApikey extends Menu
{
    public static function scriptFilter()
    {
        ScriptFilter::add(
            self::apikey(),
            self::back()
        );
    }

    private static function apikey()
    {
        global $argv;

        return Item::create()
            ->title('Enter your API token above')
            ->subtitle('Save ' . self::userInput())
            ->arg('clockify_setup_apikey_save')
            ->variable('clockify_apikey', self::userInput())
            ->icon(Icon::create('resources/icons/clockify.png'));
    }

    private static function back()
    {
        return Item::create()
            ->title('Back')
            ->subtitle('Go back to Clockify options')
            ->arg('clockify_setup')
            ->icon(Icon::create('resources/icons/clockify.png'));
    }
}
