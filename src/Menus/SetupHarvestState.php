<?php

namespace Godbout\Alfred\Time\Menus;

use Godbout\Alfred\Time\Workflow;
use Godbout\Alfred\Workflow\Icon;
use Godbout\Alfred\Workflow\Item;
use Godbout\Alfred\Workflow\ScriptFilter;

class SetupHarvestState extends Menu
{
    public static function scriptFilter()
    {
        self::saveState();

        ScriptFilter::add(
            self::stateSaved(),
            self::back()
        );
    }

    private static function saveState()
    {
        Workflow::getConfig()->write('harvest.is_active', (getenv('harvest_enabled') === 'true'));
    }

    private static function stateSaved()
    {
        return Item::create()
            ->title(self::stateTitle())
            ->subtitle('Press enter to quit the workflow.')
            ->arg('notification')
            ->icon(Icon::create('resources/icons/harvest.png'));
    }

    private static function stateTitle()
    {
        return 'Harvest ' . ((getenv('harvest_enabled') === 'true') ? 'ENABLED!' : 'DISABLED!');
    }

    private static function back()
    {
        return Item::create()
            ->title('Back')
            ->subtitle('Go back to Harvest options')
            ->arg('setup_harvest')
            ->icon(Icon::create('resources/icons/harvest.png'));
    }
}
