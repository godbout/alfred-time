<?php

require __DIR__ . '/../vendor/autoload.php';

use Godbout\Alfred\Time\Workflow;

if (getenv('action') === 'go') {
    print Workflow::go();
} else {
    print Workflow::output();
}
