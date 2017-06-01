<?php

$query = ltrim($argv[2]);

/**
 * If not config file found, call script with actions
 */
if ($config->isConfigured() === false) {
    require_once 'configure_workflow.php';
    exit();
}

require_once 'user_query_commands.php';

if (empty($data) === true) {
    require_once 'workflow_auto_commands.php';
    exit();
}

require_once 'workflow_show_lists_commands.php';

$workflow->filterResults($query);
echo $workflow->output();