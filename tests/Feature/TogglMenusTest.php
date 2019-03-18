<?php

namespace Tests\Feature;

use Tests\TestCase;
use Godbout\Alfred\Time\Workflow;

class TogglMenusTest extends TestCase
{
    /** @test */
    public function it_shows_creating_an_api_key_if_none_is_saved_in_the_config_yet()
    {
        $this->togglApikey('');

        $output = $this->reachTogglSetupMenu();

        $this->assertStringContainsString('"subtitle":"No API KEY found"', $output);
    }

    /** @test */
    public function it_shows_updating_an_api_key_is_one_is_found_in_the_config()
    {
        $this->togglApikey('e695b4364ad1ea7200035fec1bbc87cf');

        $output = $this->reachTogglSetupMenu();

        $this->assertStringContainsString('"subtitle":"Current API KEY: e695b4364ad..."', $output);
    }

    /** @test */
    public function it_can_save_the_api_key_of_the_user_in_the_config_file()
    {
        $apiKey = 'e695b4364ad1ea7200035fec1bbc87cf';

        $output = $this->reachTogglApikeySavedMenu("toggl_apikey=$apiKey");

        $fileContentAsArray = json_decode(file_get_contents($this->configFile), true);
        $this->assertArrayHasKey('api_token', $fileContentAsArray['toggl']);
        $this->assertSame($apiKey, $fileContentAsArray['toggl']['api_token']);
    }

    /** @test */
    public function it_can_enable_toggl()
    {
        $output = $this->reachTogglStateSavedMenu('toggl_enabled=true');

        $fileContentAsArray = json_decode(file_get_contents($this->configFile), true);
        $this->assertArrayHasKey('is_active', $fileContentAsArray['toggl']);
        $this->assertSame(true, $fileContentAsArray['toggl']['is_active']);
    }

    /** @test */
    public function it_can_disable_toggl()
    {
        putenv('toggl_enabled=false');

        $output = $this->reachTogglStateSavedMenu();

        $fileContentAsArray = json_decode(file_get_contents($this->configFile), true);
        $this->assertArrayHasKey('is_active', $fileContentAsArray['toggl']);
        $this->assertSame(false, $fileContentAsArray['toggl']['is_active']);
    }

    /** @test */
    public function it_shows_the_state_as_disabled_if_toggl_is_disabled()
    {
        Workflow::disableService('toggl');

        $output = $this->reachTogglSetupMenu();

        $this->assertStringContainsString('"title":"Enable"', $output);
        $this->assertStringContainsString('"subtitle":"Currently disabled"', $output);
    }

    /** @test */
    public function it_shows_the_state_as_enabled_if_toggl_is_enabled()
    {
        Workflow::enableService('toggl');

        $output = $this->reachTogglSetupMenu();

        $this->assertStringContainsString('"title":"Disable"', $output);
        $this->assertStringContainsString('"subtitle":"Currently enabled"', $output);
    }
}
