<?php

namespace Godbout\Time\Menus;

use Godbout\Alfred\Icon;
use Godbout\Alfred\Item;
use Godbout\Time\Config;
use Godbout\Time\Workflow;

class SetupTogglApikeySave extends Menu
{
    public static function content(): array
    {
        self::saveApikey();

        return [
            self::apikeySaved(),
            self::back()
        ];
    }

    private static function saveApikey()
    {
        Workflow::getConfig()->set('toggl.api_token', getenv('toggl_apikey'));

        Config::writeToFile(Workflow::getConfigFile(), Workflow::getConfig()->all());
    }

    private static function apikeySaved()
    {
        return Item::create()
            ->title('API KEY SAVED!')
            ->subtitle('You can just press Enter.')
            ->arg('notification')
            ->icon(Icon::create(__DIR__ . '/../../resources/icons/toggl.png'));
    }

    private static function back()
    {
        return Item::create()
            ->title('Back')
            ->subtitle('Go back to Toggl Setup')
            ->arg('setup_toggl')
            ->icon(Icon::create(__DIR__ . '/../../resources/icons/toggl.png'));
    }
}
