<?php

namespace Tests;

use AlfredTime\Config;
use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->workflowFolder = 'tests/AlfredWorkflowDataFolderMock';
        putenv("alfred_workflow_data={$this->workflowFolder}");
        $this->config = new Config($this->workflowFolder);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        unlink($this->workflowFolder . '/config.json');
    }

    /** @test */
    public function it_can_create_a_default_configuration_file()
    {
        $this->config->generateDefaultConfigurationFile();

        $this->assertTrue(file_exists($this->workflowFolder . '/config.json'));
    }

    /** @test */
    public function the_default_configuration_file_structure_is_correct()
    {
        $this->config->generateDefaultConfigurationFile();

        $configuration = $this->config->get();

        $expected = [
            'timer' => [
                'primary_service' => 'toggl',
                'is_running' => false,
                'toggl_id' => null,
                'harvest_id' => null,
                'description' => '',
            ],
            'toggl' => [
                'is_active' => true,
                'api_token' => '',
            ],
            'harvest' => [
                'is_active' => false,
                'domain' => '',
                'api_token' => '',
            ],
        ];
        $this->assertSame($expected, $configuration);
    }

    /** @test */
    public function it_can_check_which_services_are_activated()
    {
        $this->assertSame([], $this->config->activatedServices());


        $this->config->generateDefaultConfigurationFile();

        $this->assertSame(['toggl'], $this->config->activatedServices());


        $this->config->update('harvest', 'is_active', true);

        $this->assertSame(['toggl', 'harvest'], $this->config->activatedServices());
    }

    /** @test */
    public function it_can_check_if_the_workflow_is_configured_or_not()
    {
        $this->assertFalse($this->config->isConfigured());


        $this->config->generateDefaultConfigurationFile();

        $this->assertTrue($this->config->isConfigured());
    }
}
