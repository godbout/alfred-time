<?php

use Godbout\Alfred\Item;
use Godbout\Alfred\ScriptFilter;

ScriptFilter::add(
    Item::create()
        ->title('Toggle ENABLED!')
        ->subtitle('Press enter to quit the workflow.')
        ->arg('notification'),
    Item::create()
        ->title('Back')
        ->subtitle('Go back to Toggl options')
        ->arg('setup_toggl')
);
