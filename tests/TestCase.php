<?php

namespace Tests;

use Godbout\Time\Workflow;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    private $command = 'php src/Workflow.php';

    protected $alfredWorkflowData = __DIR__ . '/AlfredWorkflowDataFolderMock';

    protected $configFile = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setConfigFilePath();
        $this->setAlfredEnvironmentVariables();
        $this->createAlfredWorkflowDataFolder();
        $this->writeConfigFile();
    }

    public function tearDown(): void
    {
        parent::tearDown();

        Workflow::destroy();

        $this->deleteAlfredWorkflowDataFolderAndContent();
    }

    private function setConfigFilePath()
    {
        $this->configFile = $this->alfredWorkflowData . '/config.json';
    }

    private function setAlfredEnvironmentVariables()
    {
        putenv("alfred_workflow_data={$this->alfredWorkflowData}");
    }

    private function createAlfredWorkflowDataFolder()
    {
        mkdir($this->alfredWorkflowData);
    }

    private function writeConfigFile()
    {
        file_put_contents($this->configFile, json_encode([]));
    }

    protected function deleteConfigFile()
    {
        unlink($this->configFile);
    }

    protected function mockAlfredCallToScriptFilter()
    {
        return Workflow::output();
    }

    protected function deleteAlfredWorkflowDataFolderAndContent()
    {
        $this->deleteConfigFile();
        rmdir($this->alfredWorkflowData);
    }

    protected function enableToggl()
    {
        Workflow::getConfig()->set('toggl.is_active', true);
        Workflow::getConfig()->writeToFile(Workflow::getConfigFile(), Workflow::getConfig()->all());
    }

    protected function disableToggl()
    {
        Workflow::getConfig()->set('toggl.is_active', false);
        Workflow::getConfig()->writeToFile(Workflow::getConfigFile(), Workflow::getConfig()->all());
    }

    protected function reachSetupTogglMenu()
    {
        putenv('action=setup_toggl');

        return $this->mockAlfredCallToScriptFilter();
    }

    protected function togglApikey($apikey = 'e695b4364ad1ea7200035fec1bbc87cf')
    {
        Workflow::getConfig()->set('toggl.api_token', $apikey);
        Workflow::getConfig()->writeToFile(Workflow::getConfigFile(), Workflow::getConfig()->all());
    }
}
