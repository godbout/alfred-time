<?php

use Godbout\Alfred\Item;
use Godbout\Alfred\ScriptFilter;

ScriptFilter::add(
    Item::create()
        ->title('Setup API KEY')
        ->subtitle('')
        ->arg('setup_toggl_apikey'),
    Item::create()
        ->title('Enable Toggl')
        ->subtitle('')
        ->arg('setup_toggl_state')
);
