<?php

namespace Godbout\Alfred\Time\Menus\Toggl;

use Godbout\Alfred\Workflow\Icon;
use Godbout\Alfred\Workflow\Item;
use Godbout\Alfred\Time\Menus\Menu;
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
            ->arg('toggl_setup_apikey_save')
            ->variable('toggl_apikey', self::userInput())
            ->icon(Icon::create('resources/icons/toggl.png'));
    }

    private static function back()
    {
        return Item::create()
            ->title('Back')
            ->subtitle('Go back to Toggl options')
            ->arg('toggl_setup')
            ->icon(Icon::create('resources/icons/toggl.png'));
    }
}
