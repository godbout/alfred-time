<?php

$query = trim($argv[1]);
$message = '';

require 'toggl.php';
echo 'Timer started on Toggl';

require 'harvest.php';
echo "\r\n" . 'Timer started on Harvest';
