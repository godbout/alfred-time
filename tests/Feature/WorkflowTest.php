<?php

namespace Tests\Feature;

use Tests\TestCase;
use Godbout\Alfred\Time\Workflow;

class WorkflowTest extends TestCase
{
    /** @test */
    public function it_returns_a_correct_output()
    {
        $this->assertJsonStringEqualsJsonString(
            '{"items":[{"uid": "setup_timers","title":"Setup the workflow","arg":"setup","icon":{"path":"resources\/icons\/icon.png"}}]}',
            $this->reachWorkflowInitialMenu()
        );
    }

    /** @test */
    public function it_can_do_an_action_with_the_timer()
    {
        Workflow::enableService('toggl');

        $this->assertFalse($this->reachWorkflowGoAction('timer_action=running'));
    }

    /**
     * @test
     * @group timerServicesApiCalls
     */
    public function it_detects_when_a_timer_is_already_running_and_proposes_to_stop_timer_instead_of_start_timer()
    {
        Workflow::enableService('toggl');
        $this->togglApikey(getenv('TOGGL_APIKEY'));

        $service = Workflow::serviceEnabled();

        $timerId = $service->startTimer();

        $this->assertStringContainsString(
            'Stop current timer',
            Workflow::currentMenu()
        );

        $service->deleteTimer($timerId);
    }

    /** @test */
    public function it_can_disable_all_services_at_once()
    {
        Workflow::enableService('toggl');
        Workflow::enableService('harvest');

        Workflow::disableAllServices();

        $this->assertFalse(Workflow::getConfig()->read('toggl.is_active'));
        $this->assertFalse(Workflow::getConfig()->read('harvest.is_active'));
    }

    /** @test */
    public function it_can_enable_a_service()
    {
        Workflow::enableService('toggl');

        $this->assertTrue(Workflow::getConfig()->read('toggl.is_active'));
        $this->assertSame('toggl', (string) Workflow::serviceEnabled());
    }

    /** @test */
    public function it_can_disable_a_service()
    {
        Workflow::enableService('toggl');

        Workflow::disableService('toggl');

        $this->assertFalse(Workflow::getConfig()->read('toggl.is_active'));
    }

    /** @test */
    public function it_only_allows_one_service_enabled_at_a_time()
    {
        Workflow::enableService('toggl');
        Workflow::enableService('harvest');

        $this->assertTrue(Workflow::getConfig()->read('harvest.is_active'));
        $this->assertFalse(Workflow::getConfig()->read('toggl.is_active'));

        $this->assertSame('harvest', (string) Workflow::serviceEnabled());
    }
}
