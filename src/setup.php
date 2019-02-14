<?php

use Godbout\Alfred\Icon;
use Godbout\Alfred\Item;
use Godbout\Alfred\ScriptFilter;

ScriptFilter::add(
    Item::create()
        ->title('Setup Toggl')
        ->subtitle('')
        ->icon(
            Icon::create(__DIR__ . '/../resources/icons/toggl.png')
        )
        ->arg('setup_toggl'),
    Item::create()
        ->title('Setup Harvest')
        ->subtitle('')
        ->icon(
            Icon::create(__DIR__ . '/../resources/icons/harvest.png')
        )
        ->arg('setup_harvest')
);
