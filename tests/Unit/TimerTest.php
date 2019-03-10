<?php

namespace Tests\Unit;

use Tests\TestCase;
use Godbout\Alfred\Time\Timer;

class TimerTest extends TestCase
{
    /** @test */
    public function it_can_start_a_timer()
    {
        $this->togglApikey(getenv('TOGGL_APIKEY'));

        $this->assertTrue(Timer::start());
    }
}
