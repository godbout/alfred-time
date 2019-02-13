<?php

use Godbout\Alfred\Item;
use Godbout\Alfred\ScriptFilter;

ScriptFilter::add(
    Item::create()
        ->title('Setup Toggl')
        ->subtitle('')
        ->arg('setup_toggl'),
    Item::create()
        ->title('Setup Harvest')
        ->subtitle('')
        ->arg('setup_harvest')
);
