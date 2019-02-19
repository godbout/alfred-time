<?php

namespace Godbout\Time\Menus;

use Godbout\Alfred\Icon;
use Godbout\Alfred\Item;

class None extends Menu
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
