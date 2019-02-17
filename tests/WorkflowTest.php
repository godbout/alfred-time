<?php

namespace Tests;

class WorkflowTest extends TestCase
{
    /** @test */
    public function it_creates_a_config_file_with_the_default_settings_at_startup_if_none_is_found()
    {
        $this->deleteConfigFile();
        putenv('action=none');
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

        $output = $this->mockAlfredCallToScriptFilter();

        $this->assertFileExists($this->configFile);
        $this->assertEquals($defaultConfig, json_decode(file_get_contents($this->configFile), true));
    }

    /** @test */
    public function it_can_update_multiple_settings_correctly_in_the_config_file()
    {
        // $apiKey = 'e695b4364ad1ea7200035fec1bbc87cf';
        // putenv('action=setup_toggl_apikey_save');
        // putenv("toggl_apikey=$apiKey");

        // $this->mockAlfredCallToScriptFilter();

        // putenv('action=setup_toggl_state');
        // putenv('toggl_enabled=true');

        // $this->mockAlfredCallToScriptFilter();

        // $fileContentAsArray = json_decode(file_get_contents($this->configFile), true);
        // $this->assertArrayHasKey('api_token', $fileContentAsArray['toggl']);
        // $this->assertSame($apiKey, $fileContentAsArray['toggl']['api_token']);
        // $this->assertArrayHasKey('is_active', $fileContentAsArray['toggl']);
        // $this->assertSame(true, $fileContentAsArray['toggl']['is_active']);
    }
}
