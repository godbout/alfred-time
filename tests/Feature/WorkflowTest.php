<?php

namespace Tests\Feature;

use Godbout\Alfred\Time\Menus\Entrance;
use Godbout\Alfred\Time\Menus\SetupTogglState;
use Godbout\Alfred\Time\Workflow;
use Godbout\Alfred\Workflow\ScriptFilter;
use Tests\TestCase;

class WorkflowTest extends TestCase
{
    /** @test */
    public function it_returns_a_correct_output()
    {
        $this->markTestIncomplete();
        // $output = $this->reachWorkflowInitialMenu();

        // $this->assertSame(ScriptFilter::add(Entrance::content())::output(), $output);
        Workflow::output();

        $output = $this->reachTogglStateSavedMenu();

        $this->assertSame(ScriptFilter::add(SetupTogglState::content())::output(), $output);
    }
}
