<?php

namespace Godbout\Alfred\Time\Menus;

use Godbout\Alfred\Workflow\Icon;
use Godbout\Alfred\Workflow\Item;
use Godbout\Alfred\Workflow\ScriptFilter;

class Setup extends Menu
{
    public static function scriptFilter()
    {
        ScriptFilter::add(
            self::toggl(),
            self::harvest(),
            self::everhour()
        );
    }

    private static function toggl()
    {
        return Item::create()
            ->title('Setup Toggl')
            ->subtitle('')
            ->icon(Icon::create('resources/icons/toggl.png'))
            ->arg('toggl_setup');
    }

    private static function harvest()
    {
        return Item::create()
            ->title('Setup Harvest')
            ->subtitle('')
            ->icon(Icon::create('resources/icons/harvest.png'))
            ->arg('harvest_setup');
    }

    private static function everhour()
    {
        return Item::create()
            ->title('Setup Everhour')
            ->subtitle('')
            ->icon(Icon::create('resources/icons/everhour.png'))
            ->arg('everhour_setup');
    }
}
