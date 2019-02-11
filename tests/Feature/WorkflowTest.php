<?php

namespace Tests;

use AlfredTime\Config;
use PHPUnit\Framework\TestCase;

final class WorkflowTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->workflowFolder = 'tests/AlfredWorkflowDataFolderMock';
        putenv("alfred_workflow_data={$this->workflowFolder}");
    }

    public function tearDown(): void
    {
        parent::tearDown();

        unlink($this->workflowFolder . '/config.json');
    }
}
