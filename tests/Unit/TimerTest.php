<?php

namespace Tests\Unit;

use Tests\TestCase;
use Godbout\Alfred\Time\Timer;

class TimerTest extends TestCase
{
    protected function setUp()
    {
        $this->markTestIncomplete(
            'Need to be able to delete the timer LOL. Functionality not done yet.'
        );
    }

    protected function tearDown()
    {
        Timer::stop();
    }

    /** @test */
    public function it_can_start_a_timer()
    {
        $this->togglApikey(getenv('TOGGL_APIKEY'));

        $this->assertTrue(Timer::start());
    }

    /** @test */
    public function it_can_know_if_a_timer_is_currently_running()
    {
        $this->togglApikey(getenv('TOGGL_APIKEY'));

        $this->assertFalse(Timer::running());
        $this->assertTrue(Timer::start());
        $this->assertTrue(Timer::running());
    }

    /** @test */
    public function it_can_stop_a_timer()
    {
        $this->togglApikey(getenv('TOGGL_APIKEY'));

        Timer::start();

        $this->assertTrue(Timer::running());
        $this->assertTrue(Timer::stop());
        $this->assertFalse(Timer::running());
    }
}
