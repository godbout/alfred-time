<?php

use Godbout\Alfred\Item;
use Godbout\Alfred\ScriptFilter;

/**
 * Do the shit of recording API KEY in config file
 */
ScriptFilter::add(
    Item::create()
        ->title('API KEY SAVED!')
        ->subtitle('You can just press Enter.')
        ->arg('notification'),
    Item::create()
        ->title('Back')
        ->subtitle('Go back to Toggl Setup')
        ->arg('setup_toggl')
);
