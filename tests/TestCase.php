<?php

namespace Tests;

use Dotenv\Dotenv;
use Godbout\Alfred\Time\Workflow;
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

        $this->loadSecretApikeys();

        $this->resetWorkflowToDefaultSettings();
    }

    private function resetWorkflowToDefaultSettings()
    {
        Workflow::destroy();

        $this->resetConfigToDefaultSettings();

        putenv('action=');
    }

    private function resetConfigToDefaultSettings()
    {
        $this->disableAllTimerServices();
        $this->togglApikey('');
    }

    private function loadSecretApikeys()
    {
        if (file_exists(__DIR__ . '/../.env')) {
            Dotenv::create(__DIR__ . '/..//')->load();
        }
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

    protected function reachTogglStateSavedMenu($envVariable = [])
    {
        $envVariables = is_array($envVariable) ? $envVariable : [$envVariable];

        $envVariables = array_merge(['action=setup_toggl_state'], $envVariables);

        return $this->reachWorkflowMenu($envVariables);
    }

    protected function reachTogglApikeySavedMenu($envVariable = [])
    {
        $envVariables = is_array($envVariable) ? $envVariable : [$envVariable];

        $envVariables = array_merge(['action=setup_toggl_apikey_save'], $envVariables);

        return $this->reachWorkflowMenu($envVariables);
    }

    protected function reachWorkflowChooseProjectMenu()
    {
        return $this->reachWorkflowMenu('action=choose_project');
    }

    protected function reachWorkflowChooseTagMenu()
    {
        return $this->reachWorkflowMenu('action=choose_tag');
    }

    protected function reachWorkflowGoAction($envVariable = '')
    {
        return $this->reachWorkflowAction(['action=go', $envVariable]);
    }

    private function reachWorkflowAction($envVariables = [], $arguments = [])
    {
        $envVariables = is_array($envVariables) ? $envVariables : [$envVariables];

        $this->buildEnvironmentVariables($envVariables);

        $this->buildArguments($arguments);

        return Workflow::go();
    }

    private function reachWorkflowMenu($envVariables = [], $arguments = [])
    {
        $envVariables = is_array($envVariables) ? $envVariables : [$envVariables];

        $this->buildEnvironmentVariables($envVariables);

        $this->buildArguments($arguments);

        return Workflow::output();
    }

    private function buildEnvironmentVariables($envVariables = [])
    {
        foreach ($envVariables as $envVariable) {
            putenv($envVariable);
        }
    }

    private function buildArguments($arguments = [])
    {
        $arguments = is_array($arguments) ? $arguments : [$arguments];

        foreach ($arguments as $argument) {
            $_SERVER['argv'][] = $argument;
        }
    }
}
