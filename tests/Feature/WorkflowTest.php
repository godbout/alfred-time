<?php

namespace Tests\Feature;

use Tests\TestCase;
use Godbout\Alfred\Time\Menus\Entrance;
use Godbout\Alfred\Workflow\ScriptFilter;
use Godbout\Alfred\Time\Menus\SetupTogglState;

class WorkflowTest extends TestCase
{
    /** @test */
    public function it_returns_a_correct_output()
    {
        $output = $this->reachWorkflowInitialMenu();

        $this->assertSame(ScriptFilter::add(Entrance::content())::output(), $output);

        $output = $this->reachTogglStateSavedMenu();

        $this->assertSame(ScriptFilter::add(SetupTogglState::content())::output(), $output);
    }
}
