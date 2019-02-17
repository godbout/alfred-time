<?php

namespace Godbout\Time\Menus;

use Godbout\Alfred\Icon;
use Godbout\Alfred\Item;
use Godbout\Alfred\ScriptFilter;

class SetupTogglState
{
    public static function content()
    {
        $enabled = getenv('toggl_enabled') === 'true';

        $config = [
            'toggl' => [
                'is_active' => $enabled,
            ]
        ];

        file_put_contents(
            __DIR__ . '/../../tests/AlfredWorkflowDataFolderMock/config.json',
            json_encode($config, JSON_PRETTY_PRINT)
        );

        if ($enabled === true) {
            $title = 'Toggl ENABLED!';
        } else {
            $title = 'Toggl DISABLED!';
        }

        ScriptFilter::add(
            Item::create()
                ->title($title)
                ->subtitle('Press enter to quit the workflow.')
                ->arg('notification')
                ->icon(
                    Icon::create(__DIR__ . '/../resources/icons/toggl.png')
                ),
            Item::create()
                ->title('Back')
                ->subtitle('Go back to Toggl options')
                ->arg('setup_toggl')->icon(
                    Icon::create(__DIR__ . '/../resources/icons/toggl.png')
                )
        );
    }
}
