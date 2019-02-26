<?php

namespace Godbout\Alfred\Time\Menus;

use Godbout\Alfred\Workflow\Icon;
use Godbout\Alfred\Workflow\Item;

class Entrance extends Menu
{
    public static function content(): array
    {
        return [
            Item::create()
                ->title('Setup the workflow')
                ->arg('setup')
                ->icon(Icon::create(__DIR__ . '/../../resources/icons/icon.png'))
        ];
    }
}
