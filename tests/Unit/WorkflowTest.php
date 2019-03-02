<?php

namespace Tests\Unit;

use Godbout\Alfred\Time\Menus\Entrance;
use Godbout\Alfred\Time\Toggl;
use Godbout\Alfred\Time\Workflow;
use Godbout\Alfred\Workflow\ScriptFilter;
use Tests\TestCase;

class WorkflowTest extends TestCase
{
    /** @test */
    public function it_returns_the_time_service_enabled()
    {
        $this->disableAllTimerServices();

        $this->assertEmpty(Workflow::serviceEnabled());

        $this->enableToggl();

        $this->assertStringContainsString('toggl', Workflow::serviceEnabled());
    }

    /** @test */
    public function it_sets_the_toggl_enabled_argument_to_true_if_the_user_enables_toggl()
    {
        $this->disableToggl();

        $output = $this->reachTogglSetupMenu();

        $this->assertStringContainsString('"toggl_enabled":"true"', $output);
    }

    /** @test */
    public function it_returns_zero_project_if_the_service_cannot_authenticate()
    {
        $this->enableToggl();
        $this->togglApikey('wrong apikey');

        $service = Workflow::serviceEnabled();

        $this->assertSame([], $service->projects());
    }
}
