<?php

require __DIR__ . '/../vendor/autoload.php';

use Godbout\Alfred\Time\Workflow;

if (getenv('action') === 'do') {
    $result = Workflow::do();
    print Workflow::notify($result);
} else {
    print Workflow::currentMenu();
}
