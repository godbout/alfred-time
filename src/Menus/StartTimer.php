<?php

namespace Godbout\Alfred\Time\Menus;

use Godbout\Alfred\Workflow\Icon;
use Godbout\Alfred\Workflow\Item;

class StartTimer extends Menu
{
    public static function content(): array
    {
        return [
            Item::create()
                ->title('No project')
                ->subtitle('Timer will be created without a project')
                ->arg('chose_timer_project')
        ];
    }
}
