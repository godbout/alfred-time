<?php

namespace spec;

require_once __DIR__ . '/../src/Toggl.class.php';
use PhpSpec\ObjectBehavior;
use Toggl;

class TogglSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(Toggl::class);
    }

    public function it_can_stop_a_toggl_timer()
    {
        $this->stopTimer();
    }
}
