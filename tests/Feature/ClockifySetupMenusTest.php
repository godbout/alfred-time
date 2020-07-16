<?php

namespace Tests\Feature;

use Godbout\Alfred\Time\Workflow;
use Tests\TestCase;

class ClockifySetupMenusTest extends TestCase
{
    /** @test */
    public function it_proposes_to_enter_service_options_if_service_setup_is_chosen_and_offers_a_go_back_option()
    {
        $output = $this->reachClockifySetupMenu();

        $this->assertStringContainsString('clockify_setup_apikey"', $output);
        $this->assertStringContainsString('clockify_setup_state"', $output);
        $this->assertStringContainsString('"setup"', $output);
    }

    /** @test */
    public function it_shows_setting_an_api_key_if_none_is_saved_in_the_config_yet()
    {
        $this->clockifyApikey('');

        $output = $this->reachClockifySetupMenu();

        $this->assertStringContainsString('"subtitle":"No API KEY found"', $output);
    }

    /** @test */
    public function it_shows_updating_an_api_key_is_one_is_found_in_the_config()
    {
        $this->clockifyApikey('XxBtwrIBtgnj3kPX');

        $output = $this->reachClockifySetupMenu();

        $this->assertStringContainsString('"subtitle":"Current API KEY: XxBtwrIBtgn..."', $output);
    }

    /** @test */
    public function it_proposes_to_save_service_api_key_if_service_setup_apikey_is_chosen_and_offers_a_go_back_options()
    {
        $output = $this->reachClockifyApikeySetupMenu();

        $this->assertStringContainsString('clockify_setup_apikey_save"', $output);
        $this->assertStringContainsString('clockify_setup"', $output);
    }

    /** @test */
    public function it_can_save_the_api_key_of_the_user_in_the_config_file()
    {
        $apiKey = 'XxBtwrIBtgnj3kPX';

        $output = $this->reachClockifyApikeySavedMenu("clockify_apikey=$apiKey");

        $fileContentAsArray = json_decode(file_get_contents($this->configFile), true);
        $this->assertArrayHasKey('api_token', $fileContentAsArray['clockify']);
        $this->assertSame($apiKey, $fileContentAsArray['clockify']['api_token']);
    }

    /** @test */
    public function it_can_enable_the_service()
    {
        $output = $this->reachClockifyStateSavedMenu('clockify_enabled=true');

        $fileContentAsArray = json_decode(file_get_contents($this->configFile), true);
        $this->assertArrayHasKey('is_active', $fileContentAsArray['clockify']);
        $this->assertSame(true, $fileContentAsArray['clockify']['is_active']);
    }

    /** @test */
    public function it_can_disable_the_service()
    {
        putenv('clockify_enabled=false');

        $output = $this->reachClockifyStateSavedMenu();

        $fileContentAsArray = json_decode(file_get_contents($this->configFile), true);
        $this->assertArrayHasKey('is_active', $fileContentAsArray['clockify']);
        $this->assertSame(false, $fileContentAsArray['clockify']['is_active']);
    }

    /** @test */
    public function it_shows_the_state_as_disabled_if_the_is_disabled()
    {
        Workflow::disableService('clockify');

        $output = $this->reachClockifySetupMenu();

        $this->assertStringContainsString('"title":"Enable"', $output);
        $this->assertStringContainsString('"subtitle":"Currently disabled"', $output);
    }

    /** @test */
    public function it_shows_the_state_as_enabled_if_the_service_is_enabled()
    {
        Workflow::enableService('clockify');

        $output = $this->reachClockifySetupMenu();

        $this->assertStringContainsString('"title":"Disable"', $output);
        $this->assertStringContainsString('"subtitle":"Currently enabled"', $output);
    }

    /** @test */
    public function it_allows_to_quit_the_workflow_after_apikey_is_saved()
    {
        Workflow::enableService('clockify');

        $output = $this->reachClockifyApikeySavedMenu();

        $this->assertStringContainsString('"arg":"do"', $output);
        $this->assertStringContainsString('"action":"exit"', $output);
    }

    /** @test */
    public function it_allows_to_quit_the_workflow_after_status_is_changed()
    {
        Workflow::enableService('clockify');

        $output = $this->reachClockifyStateSavedMenu();

        $this->assertStringContainsString('"arg":"do"', $output);
        $this->assertStringContainsString('"action":"exit"', $output);
    }
}
