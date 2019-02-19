<?php

declare(strict_types=1);

namespace Tests;

use Godbout\Time\Workflow;

class TogglMenusTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->deleteConfigFile();
    }

    /** @test */
    public function it_can_save_the_api_key_of_the_user_in_the_config_file()
    {
        $apiKey = 'e695b4364ad1ea7200035fec1bbc87cf';
        putenv('action=setup_toggl_apikey_save');
        putenv("toggl_apikey=$apiKey");

        $output = $this->mockAlfredCallToScriptFilter();

        $fileContentAsArray = json_decode(file_get_contents($this->configFile), true);
        $this->assertArrayHasKey('api_token', $fileContentAsArray['toggl']);
        $this->assertSame($apiKey, $fileContentAsArray['toggl']['api_token']);
    }

    /** @test */
    public function it_can_enable_toggl()
    {
        putenv('action=setup_toggl_state');
        putenv('toggl_enabled=true');

        $output = $this->mockAlfredCallToScriptFilter();
        $fileContentAsArray = json_decode(file_get_contents($this->configFile), true);
        $this->assertArrayHasKey('is_active', $fileContentAsArray['toggl']);
        $this->assertSame(true, $fileContentAsArray['toggl']['is_active']);
    }

    /** @test */
    public function it_can_disable_toggl()
    {
        putenv('action=setup_toggl_state');
        putenv('toggl_enabled=false');

        $output = $this->mockAlfredCallToScriptFilter();

        $fileContentAsArray = json_decode(file_get_contents($this->configFile), true);
        $this->assertArrayHasKey('is_active', $fileContentAsArray['toggl']);
        $this->assertSame(false, $fileContentAsArray['toggl']['is_active']);
    }

    /** @test */
    public function it_shows_the_state_as_disabled_if_toggl_is_disabled()
    {
        Workflow::getConfig()->set('toggl.is_active', false);
        Workflow::getConfig()->writeToFile(Workflow::getConfigFile(), Workflow::getConfig()->all());
        putenv('action=setup_toggl');

        $output = $this->mockAlfredCallToScriptFilter();

        $this->assertStringContainsString('"subtitle":"Currently disabled"', $output);
    }

    /** @test */
    public function it_shows_the_state_as_enabled_if_toggl_is_enabled()
    {
        Workflow::getConfig()->set('toggl.is_active', true);
        Workflow::getConfig()->writeToFile(Workflow::getConfigFile(), Workflow::getConfig()->all());
        putenv('action=setup_toggl');

        $output = $this->mockAlfredCallToScriptFilter();

        $this->assertStringContainsString('"subtitle":"Currently enabled"', $output);
    }
}
