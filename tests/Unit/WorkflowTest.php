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
}
