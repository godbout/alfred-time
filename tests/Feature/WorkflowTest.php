<?php

namespace Tests\Feature;

use Tests\TestCase;
use Godbout\Time\Menus\None;
use Godbout\Alfred\ScriptFilter;
use Godbout\Time\Menus\SetupTogglState;

class WorkflowTest extends TestCase
{
    /** @test */
    public function it_creates_a_workflow_data_folder_at_startup_if_none_is_found()
    {
        $this->deleteAlfredWorkflowDataFolderAndContent();

        $this->reachWorkflowInitialMenu();

        $this->assertDirectoryExists($this->alfredWorkflowData);
    }

    /** @test */
    public function it_creates_a_config_file_with_the_default_settings_at_startup_if_none_is_found()
    {
        $this->deleteAlfredWorkflowDataFolderAndContent();
        $defaultConfig = [
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

        $this->reachWorkflowInitialMenu();

        $this->assertFileExists($this->configFile);
        $this->assertEquals($defaultConfig, json_decode(file_get_contents($this->configFile), true));
    }

    /** @test */
    public function it_can_update_multiple_settings_correctly_in_the_config_file()
    {
        $apiKey = 'e695b4364ad1ea7200035fec1bbc87cf';
        putenv("toggl_apikey=$apiKey");

        $this->reachTogglApikeySavedMenu();


        putenv('toggl_enabled=true');

        $this->reachTogglStateSetupMenu();

        $fileContentAsArray = json_decode(file_get_contents($this->configFile), true);
        $this->assertArrayHasKey('api_token', $fileContentAsArray['toggl']);
        $this->assertSame($apiKey, $fileContentAsArray['toggl']['api_token']);
        $this->assertArrayHasKey('is_active', $fileContentAsArray['toggl']);
        $this->assertSame(true, $fileContentAsArray['toggl']['is_active']);
    }

    /** @test */
    public function it_returns_a_correct_output()
    {
        $output = $this->reachWorkflowInitialMenu();

        $this->assertSame(ScriptFilter::add(None::content())::output(), $output);

        $output = $this->reachTogglStateSetupMenu();

        $this->assertSame(ScriptFilter::add(SetupTogglState::content())::output(), $output);
    }
}
