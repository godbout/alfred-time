<?php

namespace Godbout\Time\Menus;

use Godbout\Alfred\Icon;
use Godbout\Alfred\Item;
use Godbout\Alfred\ScriptFilter;

class Setup
{
    public static function content()
    {
        ScriptFilter::add(
            self::toggl(),
            self::harvest()
        );
    }

    private static function toggl()
    {
        return Item::create()
            ->title('Setup Toggl')
            ->subtitle('')
            ->icon(
                Icon::create(__DIR__ . '/../../resources/icons/toggl.png')
            )
            ->arg('setup_toggl');
    }

    private static function harvest()
    {
        return Item::create()
            ->title('Setup Harvest')
            ->subtitle('')
            ->icon(
                Icon::create(__DIR__ . '/../../resources/icons/harvest.png')
            )
            ->arg('setup_harvest');
    }
}
