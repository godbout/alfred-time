<?php

use Godbout\Alfred\Item;
use Godbout\Alfred\ScriptFilter;

ScriptFilter::add(
    Item::create()
        ->title('Set API KEY')
        ->arg('setup_toggl_apikey'),
    Item::create()
        ->title('Enable')
        ->arg('setup_toggl_state'),
    Item::create()
        ->title('Back')
        ->arg('setup')
);
