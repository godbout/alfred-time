<?php

namespace Tests\Unit;

use Godbout\Alfred\ScriptFilter;
use Godbout\Time\Menus\None;
use Godbout\Time\Menus\SetupToggl;
use Godbout\Time\Menus\SetupTogglState;
use Tests\TestCase;

class SetupTogglTest extends TestCase
{

    /** @test */
    public function it_sets_the_toggl_enabled_argument_to_true_if_the_user_enables_toggl()
    {
        $this->disableToggl();
        $this->reachSetupTogglMenu();

        $this->assertStringContainsString(
            '"toggl_enabled":"true"',
            ScriptFilter::add(None::content())::output()
        );
    }

    /** @test */
    public function it_sets_the_toggl_enabled_argument_to_false_if_the_user_disables_toggl()
    {
        $this->enableToggl();
        $this->reachSetupTogglMenu();

        $this->assertStringContainsString(
            '"toggl_enabled":"false"',
            ScriptFilter::add(None::content())::output()
        );
    }
}
