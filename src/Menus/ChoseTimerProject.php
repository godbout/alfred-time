<?php

namespace Godbout\Alfred\Time\Menus;

use Godbout\Alfred\Workflow\Item;

class ChoseTimerProject extends Menu
{
    public static function content(): array
    {
        return [
            Item::create()
                ->title('No tag')
                ->subtitle('Timer will be created without a tag')
        ];
    }

    /**
     * iTodo
     *
     * - Figure out how to choose projects, tags, etc...
     */
}
