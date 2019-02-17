<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    private $alfredWorkflowData = __DIR__ . '/AlfredWorkflowDataFolderMock';

    private $command = 'php src/Workflow.php';

    protected $configFile = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setConfigFilePath();
        $this->setAlfredEnvironmentVariables();
        $this->writeConfigFile();
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->deleteConfigFile();
    }

    protected function deleteConfigFile()
    {
        unlink($this->configFile);
    }

    private function writeConfigFile()
    {
        file_put_contents($this->configFile, json_encode([]));
    }

    private function setAlfredEnvironmentVariables()
    {
        putenv("alfred_workflow_data={$this->alfredWorkflowData}");
    }

    private function setConfigFilePath()
    {
        $this->configFile = $this->alfredWorkflowData . '/config.json';
    }

    protected function mockAlfredCallToScriptFilter()
    {
        return shell_exec($this->command);
    }
}
