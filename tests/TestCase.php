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

        Config::ifEmptyStartWith([]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        Workflow::destroy();

        Config::destroy();
    }

    protected function mockAlfredCallToScriptFilter()
    {
        return Workflow::output();
    }

    protected function deleteWorkflowDataFolderAndConfigFile()
    {
        unlink($this->configFile);
        rmdir($this->workflowDataFolder);
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

    protected function reachWorkflowInitialMenu()
    {
        return $this->reachWorkflowMenu('action=entrance');
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

    protected function reachTogglStateSavedMenu()
    {
        return $this->reachWorkflowMenu('action=setup_toggl_state');
    }

    protected function reachTogglApikeySavedMenu()
    {
        return $this->reachWorkflowMenu('action=setup_toggl_apikey_save');
    }

    private function reachWorkflowMenu($environmentVariable)
    {
        putenv($environmentVariable);

        return $this->mockAlfredCallToScriptFilter();
    }
}
