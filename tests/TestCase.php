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

        $this->setUpWorkflowDataFolder();

        $this->setUpConfigFilePath();

        $this->resetWorkflowToDefaultSettings();

        $this->loadSecretApikeys();
    }

    private function setUpWorkflowDataFolder()
    {
        putenv("alfred_workflow_data={$this->workflowDataFolder}");
    }

    private function setUpConfigFilePath()
    {
        $this->configFile = $this->workflowDataFolder . '/config.json';
    }

    private function resetWorkflowToDefaultSettings()
    {
        $this->resetWorkflowSingleton();

        $this->resetConfigToDefaultSettings();

        $this->resetEnvVariables();

        $this->resetScriptArguments();
    }

    private function resetWorkflowSingleton()
    {
        Workflow::destroy();
    }

    private function resetConfigToDefaultSettings()
    {
        $this->disableAllTimerServices();

        $this->togglApikey('');
    }

    private function resetEnvVariables()
    {
        putenv('action=');
    }

    private function resetScriptArguments()
    {
        global $argv;
        $argv = [];
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

    protected function reachTogglStateSavedMenu($envVariables = [])
    {
        $envVariables = array_merge(['action=setup_toggl_state'], (array) $envVariables);

        return $this->reachWorkflowMenu($envVariables);
    }

    protected function reachTogglApikeySavedMenu($envVariables = [])
    {
        $envVariables = array_merge(['action=setup_toggl_apikey_save'], (array) $envVariables);

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

    protected function reachWorkflowGoAction($envVariables = [])
    {
        $envVariables = array_merge(['action=do'], (array) $envVariables);

        return $this->reachWorkflowAction($envVariables);
    }

    private function reachWorkflowAction($envVariables = [], $arguments = [])
    {
        $this->buildWorkflowWorld($envVariables, $arguments);

        return Workflow::do();
    }

    private function reachWorkflowMenu($envVariables = [], $arguments = [])
    {
        $this->buildWorkflowWorld($envVariables, $arguments);

        return Workflow::currentMenu();
    }

    private function buildWorkflowWorld($envVariables = [], $arguments = [])
    {
        $this->buildEnvironmentVariables((array) $envVariables);

        $this->buildArguments((array) $arguments);
    }

    private function buildEnvironmentVariables(array $envVariables = [])
    {
        foreach ($envVariables as $envVariable) {
            putenv($envVariable);
        }
    }

    private function buildArguments(array $arguments = [])
    {
        global $argv;

        $argv[0] = 'src/app.php';

        foreach ($arguments as $argument) {
            $argv[] = $argument;
        }
    }
}
