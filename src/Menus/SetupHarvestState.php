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
        Workflow::disableAllServices();

        getenv('harvest_enabled') === 'true'
            ? Workflow::enableService('harvest')
            : Workflow::disableService('harvest');
    }

    private static function stateSaved()
    {
        return Item::create()
            ->title(self::stateTitle())
            ->subtitle(self::stateSubtitle())
            ->arg('notification')
            ->icon(Icon::create('resources/icons/harvest.png'));
    }

    private static function stateTitle()
    {
        return 'Harvest ' . ((getenv('harvest_enabled') === 'true') ? 'ENABLED!' : 'DISABLED!');
    }

    protected static function stateSubtitle()
    {
        return ((getenv('harvest_enabled') === 'true')
            ? 'Other services disabled. '
            : ''
        ) . 'You may press enter to quit the workflow';
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
