<?php

namespace Godbout\Alfred\Time\Menus;

use Godbout\Alfred\Workflow\Icon;
use Godbout\Alfred\Workflow\Item;
use Godbout\Alfred\Workflow\ScriptFilter;

class SetupHarvestApitoken extends Menu
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
            ->arg('setup_harvest_apitoken_save')
            ->variable('harvest_apitoken', self::userInput())
            ->icon(Icon::create('resources/icons/harvest.png'));
    }

    private static function back()
    {
        return Item::create()
            ->title('Back')
            ->subtitle('Go back to Harvest credentials options')
            ->arg('setup_harvest_credentials')
            ->icon(Icon::create('resources/icons/harvest.png'));
    }
}
