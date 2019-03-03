<?php

namespace Tests\Feature;

use Tests\TestCase;

class TimerMenusTest extends TestCase
{
    /** @test */
    public function it_proposes_to_start_a_timer_if_there_is_at_least_one_timer_service_enabled()
    {
        $this->disableAllTimerServices();
        $this->enableToggl();

        $output = $this->reachWorkflowInitialMenu();

        $this->assertStringContainsString('"arg":"choose_project"', $output);
    }

    /** @test */
    public function it_does_not_propose_to_start_a_timer_if_there_is_no_timer_services_enabled()
    {
        $this->disableAllTimerServices();

        $output = $this->reachWorkflowInitialMenu();

        $this->assertStringNotContainsString('"arg":"setup_timer"', $output);
    }

    /** @test */
    public function it_proposes_a_choice_of_projects_after_having_entered_the_timer_description()
    {
        $this->disableAllTimerServices();
        $this->enableToggl();
        $this->togglApikey('wrong key');

        $output = $this->reachWorkflowChooseProjectMenu();

        $this->assertStringContainsString('"title":"No project"', $output);
    }

    /** @test */
    public function it_proposes_a_choice_of_tags_after_having_chosen_a_project()
    {
        $this->disableAllTimerServices();
        $this->enableToggl();
        $this->togglApikey('wrong key');

        $output = $this->reachWorkflowChooseTagMenu();

        $this->assertStringContainsString('"title":"No tag"', $output);
    }
}
