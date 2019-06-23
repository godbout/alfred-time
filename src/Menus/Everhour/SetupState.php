<?php

namespace Godbout\Alfred\Time\Menus\Everhour;

use Godbout\Alfred\Time\Workflow;
use Godbout\Alfred\Workflow\Icon;
use Godbout\Alfred\Workflow\Item;
use Godbout\Alfred\Time\Menus\Menu;
use Godbout\Alfred\Workflow\ScriptFilter;

class SetupState extends Menu
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

        self::toEnable() ? Workflow::enableService('everhour') : Workflow::disableService('everhour');
    }

    private static function stateSaved()
    {
        return Item::create()
            ->title(self::stateTitle())
            ->subtitle(self::stateSubtitle())
            ->icon(Icon::create('resources/icons/everhour.png'))
            ->arg('do')
            ->variable('timer_action', 'exit');
    }

    private static function stateTitle()
    {
        return 'Everhour ' . (self::toEnable() ? 'ENABLED!' : 'DISABLED!');
    }

    protected static function stateSubtitle()
    {
        return (self::toEnable() ? 'Other services disabled. ' : '') . 'You may press enter to quit the workflow';
    }

    private static function back()
    {
        return Item::create()
            ->title('Back')
            ->subtitle('Go back to Everhour options')
            ->arg('everhour_setup')
            ->icon(Icon::create('resources/icons/everhour.png'));
    }

    protected static function toEnable()
    {
        return getenv('everhour_enabled') === 'true';
    }
}
