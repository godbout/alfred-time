<?php

namespace Tests\Unit;

use Tests\TestCase;
use Godbout\Alfred\Time\Workflow;
use Godbout\Alfred\Time\Menus\Entrance;
use Godbout\Alfred\Workflow\ScriptFilter;

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
}
