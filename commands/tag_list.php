<?php

require 'vendor/autoload.php';
require 'AlfredTime.class.php';

use Alfred\Workflows\Workflow;

$workflow = new Workflow;
$alfredTime = new AlfredTime;

$query = trim($argv[1]);

$tags = $alfredTime->getTags();

foreach ($tags as $tag) {
    $workflow->result()
        ->uid('')
        ->arg($tag['id'])
        ->title($tag['name'])
        ->type('default')
        ->valid(true);
}

$workflow->filterResults($query);

echo $workflow->output();
