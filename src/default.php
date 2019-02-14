<?php

use Godbout\Alfred\Icon;
use Godbout\Alfred\Item;
use Godbout\Alfred\ScriptFilter;

ScriptFilter::add(
    Item::create()
        ->title('Setup the workflow')
        ->arg('setup')
        ->icon(
            Icon::create(__DIR__ . '/../resources/icons/icon.png')
        )
);
