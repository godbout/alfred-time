<?php

namespace Godbout\Alfred\Time\Menus;

use Godbout\Alfred\Time\Workflow;
use Godbout\Alfred\Workflow\Icon;
use Godbout\Alfred\Workflow\Item;
use Godbout\Alfred\Workflow\ScriptFilter;

class SetupHarvestApitokenSave extends Menu
{
    public static function scriptFilter()
    {
        self::saveApitoken();

        ScriptFilter::add(
            self::apitokenSaved(),
            self::back()
        );
    }

    private static function saveApitoken()
    {
        Workflow::getConfig()->write('harvest.api_token', getenv('harvest_apitoken'));
    }

    private static function apitokenSaved()
    {
        return Item::create()
            ->title('API TOKEN SAVED!')
            ->subtitle('You can just press Enter.')
            ->icon(Icon::create('resources/icons/harvest.png'))
            ->arg('do')
            ->variable('timer_action', 'exit');
    }

    private static function back()
    {
        return Item::create()
            ->title('Back')
            ->subtitle('Go back to Harvest Setup')
            ->arg('setup_harvest_credentials')
            ->icon(Icon::create('resources/icons/harvest.png'));
    }
}
