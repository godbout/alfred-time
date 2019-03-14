<?php

namespace Godbout\Alfred\Time\Menus;

use Godbout\Alfred\Time\Workflow;
use Godbout\Alfred\Workflow\Icon;
use Godbout\Alfred\Workflow\Item;
use Godbout\Alfred\Workflow\ScriptFilter;

class SetupTogglState extends Menu
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
        Workflow::getConfig()->write('toggl.is_active', (getenv('toggl_enabled') === 'true'));
    }

    private static function stateSaved()
    {
        return Item::create()
            ->title(self::stateTitle())
            ->subtitle('Press enter to quit the workflow.')
            ->arg('notification')
            ->icon(Icon::create('resources/icons/toggl.png'));
    }

    private static function stateTitle()
    {
        return 'Toggl ' . ((getenv('toggl_enabled') === 'true') ? 'ENABLED!' : 'DISABLED!');
    }

    private static function back()
    {
        return Item::create()
            ->title('Back')
            ->subtitle('Go back to Toggl options')
            ->arg('setup_toggl')
            ->icon(Icon::create('resources/icons/toggl.png'));
    }
}
