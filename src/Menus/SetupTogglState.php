<?php

namespace Godbout\Time\Menus;

use Godbout\Alfred\Icon;
use Godbout\Alfred\Item;
use Godbout\Time\Config;
use Godbout\Time\Workflow;

class SetupTogglState extends Menu
{
    public static function content(): array
    {
        self::saveState();

        return [
            self::stateSaved(),
            self::back()
        ];
    }

    private static function saveState()
    {
        Workflow::getConfig()->write('toggle.is_active', (getenv('toggl_enabled') === 'true'));
    }

    private static function stateSaved()
    {
        return Item::create()
            ->title(self::stateTitle())
            ->subtitle('Press enter to quit the workflow.')
            ->arg('notification')
            ->icon(Icon::create(__DIR__ . '/../../resources/icons/toggl.png'));
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
            ->icon(Icon::create(__DIR__ . '/../../resources/icons/toggl.png'));
    }
}
