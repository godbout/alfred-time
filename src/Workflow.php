<?php

namespace Godbout\Time;

use Godbout\Alfred\ScriptFilter;

require __DIR__ . '/../vendor/autoload.php';

$action = getenv('action');

ScriptFilter::create();

$class = __NAMESPACE__ . '\\' . str_replace('_', '', ucwords($action === false ? 'none' : $action, '_'));
$class::content();

echo ScriptFilter::output();
