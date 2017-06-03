<?php

$data = ['action' => 'setup'];

$workflow->result()
    ->uid('')
    ->arg(json_encode($data))
    ->title('No config file found')
    ->subtitle('Generate and edit the config file')
    ->type('default')
    ->valid(true);

echo $workflow->output();
