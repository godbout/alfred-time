<?php

namespace Tests\Unit;

use Tests\TestCase;
use Godbout\Alfred\Time\Workflow;

class WorkflowTest extends TestCase
{
    /** @test */
    public function it_returns_the_time_service_enabled()
    {
        $this->assertEmpty(Workflow::serviceEnabled());

        Workflow::enableService('toggl');

        $this->assertStringContainsString('toggl', Workflow::serviceEnabled());
    }

    /** @test */
    public function it_sets_the_toggl_enabled_argument_to_true_if_the_user_enables_toggl()
    {
        Workflow::disableService('toggl');

        $output = $this->reachTogglSetupMenu();

        $this->assertStringContainsString('"toggl_enabled":"true"', $output);
    }
}
