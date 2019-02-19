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
        Workflow::getConfig()->set('toggl.is_active', getenv('toggl_enabled') === 'true');

        Config::writeToFile(Workflow::getConfigFile(), Workflow::getConfig()->all());
    }

    private static function stateSaved()
    {
        if (getenv('toggl_enabled') === 'true') {
            $title = 'Toggl ENABLED!';
        } else {
            $title = 'Toggl DISABLED!';
        }

        return Item::create()
            ->title($title)
            ->subtitle('Press enter to quit the workflow.')
            ->arg('notification')
            ->icon(
                Icon::create(__DIR__ . '/../../resources/icons/toggl.png')
            );
    }

    private static function back()
    {
        return Item::create()
            ->title('Back')
            ->subtitle('Go back to Toggl options')
            ->arg('setup_toggl')->icon(
                Icon::create(__DIR__ . '/../../resources/icons/toggl.png')
            );
    }
}
