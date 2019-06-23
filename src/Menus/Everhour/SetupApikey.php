<?php

namespace Godbout\Alfred\Time\Menus\Everhour;

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
            ->arg('everhour_setup_apikey_save')
            ->variable('everhour_apikey', self::userInput())
            ->icon(Icon::create('resources/icons/everhour.png'));
    }

    private static function back()
    {
        return Item::create()
            ->title('Back')
            ->subtitle('Go back to Everhour options')
            ->arg('everhour_setup')
            ->icon(Icon::create('resources/icons/everhour.png'));
    }
}
