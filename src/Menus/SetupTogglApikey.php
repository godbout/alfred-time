<?php

namespace Godbout\Alfred\Time\Menus;

use Godbout\Alfred\Workflow\Icon;
use Godbout\Alfred\Workflow\Item;

class SetupTogglApikey extends Menu
{
    public static function content(): array
    {
        return [
            self::apikey(),
            self::back(),
        ];
    }

    private static function apikey()
    {
        global $argv;

        return Item::create()
            ->title('Enter your API KEY above')
            ->subtitle('Save ' . self::userInput())
            ->arg('setup_toggl_apikey_save')
            ->variable('toggl_apikey', self::userInput())
            ->icon(Icon::create(__DIR__ . '/../../resources/icons/toggl.png'));
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
