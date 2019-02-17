<?php

namespace Godbout\Time\Menus;

use Godbout\Alfred\Icon;
use Godbout\Alfred\Item;
use Godbout\Alfred\ScriptFilter;

class SetupTogglState
{
    public static function content()
    {
        self::saveState();

        ScriptFilter::add(
            self::stateSaved(),
            self::back()
        );
    }

    private static function saveState()
    {
        $config = [
            'toggl' => [
                'is_active' => getenv('toggl_enabled') === 'true'
            ]
        ];

        file_put_contents(
            getenv('alfred_workflow_data') . '/config.json',
            json_encode($config, JSON_PRETTY_PRINT)
        );
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
