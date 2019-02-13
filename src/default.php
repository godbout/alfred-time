<?php

use Godbout\Alfred\Item;
use Godbout\Alfred\ScriptFilter;

ScriptFilter::add(
    Item::create()
        ->title('Setup the workflow')
        ->subtitle('No config file has been found. Time for setup!')
        ->arg('setup')
);
