<?php

namespace Godbout\Time\Menus;

use Godbout\Alfred\Icon;
use Godbout\Alfred\Item;

class SetupToggl extends Menu
{
    public static function content(): array
    {
        return [
            self::apikey(),
            self::state(),
            self::back()
        ];
    }

    private static function apikey()
    {
        return Item::create()
            ->title('Set API KEY')
            ->arg('setup_toggl_apikey')
            ->icon(
                Icon::create(__DIR__ . '/../../resources/icons/toggl.png')
            );
    }

    private static function state()
    {
        return Item::create()
            ->title('Enable')
            ->subtitle('Currently disabled')
            ->arg('setup_toggl_state')
            ->variable('toggl_enabled', true)
            ->icon(
                Icon::create(__DIR__ . '/../../resources/icons/toggl.png')
            );
    }

    private static function back()
    {
        return Item::create()
            ->title('Back')
            ->arg('setup')
            ->icon(
                Icon::create(__DIR__ . '/../../resources/icons/icon.png')
            );
    }
}
