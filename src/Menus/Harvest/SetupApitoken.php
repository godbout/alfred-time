<?php

namespace Godbout\Alfred\Time\Menus\Harvest;

use Godbout\Alfred\Workflow\Icon;
use Godbout\Alfred\Workflow\Item;
use Godbout\Alfred\Time\Menus\Menu;
use Godbout\Alfred\Workflow\ScriptFilter;

class SetupApitoken extends Menu
{
    public static function scriptFilter()
    {
        ScriptFilter::add(
            self::apitoken(),
            self::back()
        );
    }

    private static function apitoken()
    {
        global $argv;

        return Item::create()
            ->title('Enter your API token above')
            ->subtitle('Save ' . self::userInput())
            ->arg('harvest_setup_apitoken_save')
            ->variable('harvest_apitoken', self::userInput())
            ->icon(Icon::create('resources/icons/harvest.png'));
    }

    private static function back()
    {
        return Item::create()
            ->title('Back')
            ->subtitle('Go back to Harvest credentials options')
            ->arg('harvest_setup_credentials')
            ->icon(Icon::create('resources/icons/harvest.png'));
    }
}
