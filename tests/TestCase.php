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
}
