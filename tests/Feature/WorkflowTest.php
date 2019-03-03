<?php

namespace Tests\Feature;

use Tests\TestCase;

class WorkflowTest extends TestCase
{
    /** @test */
    public function it_returns_a_correct_output()
    {
        $this->disableAllTimerServices();

        $this->assertJsonStringEqualsJsonString(
            '{"items":[{"title":"Setup the workflow","arg":"setup","icon":{"path":"resources\/icons\/icon.png"}}]}',
            $this->reachWorkflowInitialMenu(null, '')
        );

        $this->assertJsonStringEqualsJsonString(
            '{"items":[{"title":"Setup Toggl","subtitle":"","icon":{"path":"resources\/icons\/toggl.png"},"arg":"setup_toggl"},{"title":"Setup Harvest","subtitle":"","icon":{"path":"resources\/icons\/harvest.png"},"arg":"setup_harvest"}]}',
            $this->reachWorkflowSetupMenu()
        );
    }
}
