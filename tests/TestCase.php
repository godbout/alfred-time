<?php

namespace Tests;

use Godbout\Alfred\Time\Workflow;
use Godbout\Alfred\Workflow\Config;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected $workflowDataFolder = __DIR__ . '/mo.com.sleeplessmind.time';

    protected $configFile = null;


    protected function setUp(): void
    {
        parent::setUp();

        $this->configFile = $this->workflowDataFolder . '/config.json';

        putenv("alfred_workflow_data={$this->workflowDataFolder}");
    }

    protected function disableAllTimerServices()
    {
        $this->disableToggl();
    }

    protected function enableToggl()
    {
        Workflow::getConfig()->write('toggl.is_active', true);
    }

    protected function disableToggl()
    {
        Workflow::getConfig()->write('toggl.is_active', false);
    }

    protected function togglApikey($apikey = 'e695b4364ad1ea7200035fec1bbc87cf')
    {
        Workflow::getConfig()->write('toggl.api_token', $apikey);
    }

    protected function reachWorkflowInitialMenu($envVariables = [], $arguments = [])
    {
        return $this->reachWorkflowMenu($envVariables, $arguments);
    }

    protected function reachWorkflowSetupMenu()
    {
        return $this->reachWorkflowMenu('action=setup');
    }

    protected function reachTogglSetupMenu()
    {
        return $this->reachWorkflowMenu('action=setup_toggl');
    }

    protected function reachTogglApikeySetupMenu()
    {
        return $this->reachWorkflowMenu('action=setup_toggl_apikey');
    }

    protected function reachTogglStateSavedMenu($envVariable = '')
    {
        return $this->reachWorkflowMenu(['action=setup_toggl_state', $envVariable]);
    }

    protected function reachTogglApikeySavedMenu($envVariable = '')
    {
        return $this->reachWorkflowMenu(['action=setup_toggl_apikey_save', $envVariable]);
    }

    private function reachWorkflowMenu($envVariables = [], $arguments = [])
    {
        return $this->mockAlfredCallToScriptFilter($envVariables, $arguments);
    }

    protected function mockAlfredCallToScriptFilter($envVariables = [], $arguments = [])
    {
        $envCommand = $this->buildEnvironmentVariables($envVariables);

        $phpCommand = $this->buildPHPCommand($arguments);

        return shell_exec("$envCommand $phpCommand");
    }

    private function buildEnvironmentVariables($envVariables = [])
    {
        $envVariables = is_array($envVariables) ? $envVariables : [$envVariables];

        $envCommand = 'env -i alfred_workflow_data=' . $this->workflowDataFolder;

        foreach ($envVariables as $variable) {
            $envCommand .= " $variable";
        }

        return $envCommand;
    }

    private function buildPHPCommand($arguments = [])
    {
        $arguments = is_array($arguments) ? $arguments : [$arguments];

        $phpCommand = 'php ' . __DIR__ . '/../src/app.php';

        foreach ($arguments as $argument) {
            $phpCommand .= " $argument";
        }

        return $phpCommand;
    }
}
