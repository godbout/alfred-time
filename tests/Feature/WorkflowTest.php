<?php

namespace Tests\Feature;

use Godbout\Alfred\Time\Workflow;
use Tests\TestCase;

class WorkflowTest extends TestCase
{
    /** @test */
    public function it_returns_a_correct_output()
    {
        $this->assertJsonStringEqualsJsonString(
            '{"items":[{"title":"Setup the workflow","arg":"setup","icon":{"path":"resources\/icons\/icon.png"}}]}',
            $this->reachWorkflowInitialMenu()
        );

        Workflow::destroy();

        $this->assertJsonStringEqualsJsonString(
            '{"items":[{"title":"Setup Toggl","subtitle":"","icon":{"path":"resources\/icons\/toggl.png"},"arg":"setup_toggl"},{"title":"Setup Harvest","subtitle":"","icon":{"path":"resources\/icons\/harvest.png"},"arg":"setup_harvest"}]}',
            $this->reachWorkflowSetupMenu()
        );
    }

    /** @test */
    public function it_can_do_an_action_with_the_timer()
    {
        $this->assertTrue($this->reachWorkflowGoAction('timer_action=stop'));
    }

    /**
     * @test
     * @group timerServicesApiCalls
     */
    public function it_detects_when_a_timer_is_already_running_and_proposes_to_stop_timer_instead_of_start_timer()
    {
        $this->enableToggl();
        $this->togglApikey(getenv('TOGGL_APIKEY'));

        $service = Workflow::serviceEnabled();

        $timerId = $service->startTimer();

        $this->assertStringContainsString(
            'Stop current timer',
            Workflow::output()
        );

        $service->deleteTimer($timerId);
    }
}
