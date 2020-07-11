<?php

require __DIR__ . '/../vendor/autoload.php';

use Godbout\Alfred\Time\Workflow;

if (getenv('next') === 'do') {
    $result = Workflow::do();

    if (getenv('action') !== 'exit') {
        print Workflow::notify($result);
    }
} else {
    print Workflow::currentMenu();
}
