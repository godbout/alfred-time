<?php

require __DIR__ . '/../vendor/autoload.php';

use Godbout\Alfred\Time\Workflow;

if (getenv('action') === 'do') {
    Workflow::do();
} else {
    print Workflow::currentMenu();
}
