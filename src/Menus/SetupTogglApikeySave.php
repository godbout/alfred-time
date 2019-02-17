<?php

namespace Godbout\Time\Menus;

use Godbout\Alfred\Icon;
use Godbout\Alfred\Item;
use Godbout\Alfred\ScriptFilter;

class SetupTogglApikeySave
{
    public static function content()
    {
        self::saveApikey();

        ScriptFilter::add(
            self::apikeySaved(),
            self::back()
        );
    }

    private static function saveApikey()
    {
        $config = [
            'toggl' => [
                'api_token' => getenv('toggl_apikey'),
            ]
        ];

        file_put_contents(
            getenv('alfred_workflow_data') . '/config.json',
            json_encode($config, JSON_PRETTY_PRINT)
        );
    }

    private static function apikeySaved()
    {
        return Item::create()
            ->title('API KEY SAVED!')
            ->subtitle('You can just press Enter.')
            ->arg('notification')
            ->icon(
                Icon::create(__DIR__ . '/../../resources/icons/toggl.png')
            );
    }

    private static function back()
    {
        return Item::create()
            ->title('Back')
            ->subtitle('Go back to Toggl Setup')
            ->arg('setup_toggl')
            ->icon(
                Icon::create(__DIR__ . '/../../resources/icons/toggl.png')
            );
    }
}
