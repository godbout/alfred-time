<?php

namespace Godbout\Time\Menus;

use Godbout\Alfred\Icon;
use Godbout\Alfred\Item;

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
            ->subtitle('Save ' . trim($argv[1] ?? ''))
            ->arg('setup_toggl_apikey_save')
            ->variable('toggl_apikey', trim($argv[1] ?? ''))
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
