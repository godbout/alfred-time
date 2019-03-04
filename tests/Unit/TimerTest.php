<?php

namespace Tests\Unit;

use Tests\TestCase;
use Godbout\Alfred\Time\Timer;

class TimerTest extends TestCase
{
    /** @test */
    public function it_can_start_a_timer()
    {
        $this->assertTrue(Timer::start());

        /**
         * iTodo
         *
         * - Add better tests for this (and develop source)
         */
    }
}
