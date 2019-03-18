<?php

namespace Tests\Unit;

use Tests\TestCase;
use Godbout\Alfred\Time\Workflow;

class SetupTogglTest extends TestCase
{

    /** @test */
    public function it_sets_the_toggl_enabled_argument_to_true_if_the_user_enables_toggl()
    {
        Workflow::disableService('toggl');

        $output = $this->reachTogglSetupMenu();

        $this->assertStringContainsString('"toggl_enabled":"true"', $output);
    }

    /** @test */
    public function it_sets_the_toggl_enabled_argument_to_false_if_the_user_disables_toggl()
    {
        Workflow::enableService('toggl');

        $output = $this->reachTogglSetupMenu();

        $this->assertStringContainsString('"toggl_enabled":"false"', $output);
    }
}
