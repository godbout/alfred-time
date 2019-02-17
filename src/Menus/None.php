<?php

namespace Godbout\Time\Menus;

use Godbout\Alfred\Icon;
use Godbout\Alfred\Item;
use Godbout\Alfred\ScriptFilter;

class None
{
    public static function content()
    {
        ScriptFilter::add(
            Item::create()
                ->title('Setup the workflow')
                ->arg('setup')
                ->icon(
                    Icon::create(__DIR__ . '/../resources/icons/icon.png')
                )
        );
    }
}
