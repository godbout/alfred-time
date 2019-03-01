<?php

namespace Godbout\Alfred\Time\Menus;

use Godbout\Alfred\Workflow\Icon;
use Godbout\Alfred\Workflow\Item;

class Setup extends Menu
{
    public static function content(): array
    {
        return [
            self::toggl(),
            self::harvest()
        ];
    }

    private static function toggl()
    {
        return Item::create()
            ->title('Setup Toggl')
            ->subtitle('')
            ->icon(Icon::create('resources/icons/toggl.png'))
            ->arg('setup_toggl');
    }

    private static function harvest()
    {
        return Item::create()
            ->title('Setup Harvest')
            ->subtitle('')
            ->icon(Icon::create('resources/icons/harvest.png'))
            ->arg('setup_harvest');
    }
}
