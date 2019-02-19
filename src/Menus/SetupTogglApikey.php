<?php

namespace Godbout\Time\Menus;

use Godbout\Alfred\Icon;
use Godbout\Alfred\Item;

class SetupTogglApikey extends Menu
{
    public static function content(): array
    {
        global $argv;

        return [
            Item::create()
                ->title('Enter your API KEY above')
                ->subtitle('Your API KEY will be saved.')
                ->arg('setup_toggl_apikey_save')
                ->variable('toggl_apikey', trim($argv[1] ?? ''))
                ->icon(
                    Icon::create(__DIR__ . '/../../resources/icons/toggl.png')
                ),
            Item::create()
                ->title('Back')
                ->subtitle('Go back to Toggl options')
                ->arg('setup_toggl')
                ->icon(
                    Icon::create(__DIR__ . '/../../resources/icons/toggl.png')
                )
        ];
    }
}
