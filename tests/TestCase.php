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

        $this->deleteWorkflowDataFolderAndContent();
    }

    protected function deleteConfigFile()
    {
        unlink($this->configFile);
    }

    protected function mockAlfredCallToScriptFilter()
    {
        return Workflow::output();
    }

    protected function deleteWorkflowDataFolderAndContent()
    {
        $this->deleteConfigFile();

        rmdir($this->workflowDataFolder);

        // if (file_exists($this->configFile)) {
        //     unlink($this->configFile);
        // }

        // if (file_exists($this->workflowDataFolder)) {
        //     rmdir($this->workflowDataFolder);
        // }
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
        return $this->reachWorkflowMenu('action=none');
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
