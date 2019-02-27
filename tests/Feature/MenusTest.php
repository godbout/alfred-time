<?php

namespace Tests\Feature;

use Godbout\Alfred\Time\Menus\Entrance;
use Tests\TestCase;

class MenusTest extends TestCase
{
    /** @test */
    public function it_proposes_to_setup_the_workflow_at_first_menu_if_no_timer_services_enabled()
    {
        $this->disableAllTimerServices();

        $output = $this->reachWorkflowInitialMenu();

        $this->assertStringContainsString('"arg":"setup"', $output);
    }

    /** @test */
    public function it_proposes_to_setup_the_workflow_at_first_menu_if_a_timer_service_is_enabled_but_there_is_no_user_input()
    {
        $output = $this->reachWorkflowInitialMenu();

        $this->assertStringContainsString('"arg":"setup"', $output);
    }

    /** @test */
    public function it_does_not_propose_to_setup_the_workflow_at_first_menu_if_a_timer_service_is_enabled_and_there_is_user_input()
    {
        $this->enableToggl();

        $output = $this->reachWorkflowInitialMenu(null, 'some typing made by the user');

        $this->assertStringNotContainsString('"arg":"setup"', $output);
    }

    /** @test */
    public function it_proposes_to_start_a_timer_if_there_is_at_least_one_timer_service_enabled()
    {
        $this->disableAllTimerServices();
        $this->enableToggl();

        $output = $this->reachWorkflowInitialMenu();

        $this->assertStringContainsString('"arg":"start_timer"', $output);
    }

    /** @test */
    public function it_does_not_propose_to_start_a_timer_if_there_is_no_timer_services_enabled()
    {
        $this->disableAllTimerServices();

        $output = $this->reachWorkflowInitialMenu();

        $this->assertStringNotContainsString('"arg":"setup_timer"', $output);
    }

    /** @test */
    public function it_proposes_to_setup_toggl_and_harvest_if_setup_is_accepted_and_offers_a_go_back_option()
    {
        $output = $this->reachWorkflowSetupMenu();

        $this->assertStringContainsString('"setup_toggl"', $output);
        $this->assertStringContainsString('"setup_harvest"', $output);
    }

    /** @test */
    public function it_proposes_to_enter_toggl_options_if_toggl_setup_is_chosen_and_offers_a_go_back_option()
    {
        $output = $this->reachTogglSetupMenu();

        $this->assertStringContainsString('"setup_toggl_apikey"', $output);
        $this->assertStringContainsString('"setup_toggl_state"', $output);
        $this->assertStringContainsString('"setup"', $output);
    }

    /** @test */
    public function it_proposes_to_save_toggl_api_key_if_setup_toggl_apikey_is_chosen_and_offers_a_go_back_options()
    {
        $output = $this->reachTogglApikeySetupMenu();

        $this->assertStringContainsString('"setup_toggl_apikey_save"', $output);
        $this->assertStringContainsString('"setup_toggl"', $output);
    }

    /** @test */
    public function it_proposes_a_go_back_option_if_toggl_api_key_is_saved()
    {
        $output = $this->reachTogglApikeySavedMenu();

        $this->assertStringContainsString('"setup_toggl"', $output);
    }

    /** @test */
    public function it_proposes_a_go_back_option_if_toggl_state_is_saved()
    {
        $output = $this->reachTogglStateSavedMenu();

        $this->assertStringContainsString('"setup_toggl"', $output);
    }
}
