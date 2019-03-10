<?php

require __DIR__ . '/../vendor/autoload.php';

use Godbout\Alfred\Time\Workflow;

if (getenv('action') === 'do') {
    Workflow::do();

    print "Hey, looks like we just did something with a timer and it worked.";
} else {
    print Workflow::currentMenu();
}
