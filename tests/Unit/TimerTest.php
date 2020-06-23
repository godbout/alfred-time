<?php

namespace Tests\Unit;

use Tests\TestCase;
use Godbout\Alfred\Time\Timer;
use Godbout\Alfred\Time\Workflow;

class TimerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->togglApikey(getenv('TOGGL_APIKEY'));

        Workflow::enableService('toggl');

        $this->setTogglTimerAttributes();

        sleep(2);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        if ($timerId = Timer::running()) {
            Timer::stop();
            Workflow::serviceEnabled()->deleteTimer($timerId);
        }
    }

    /**
     * @test
     * @group timerServicesApiCalls
     */
    public function it_can_start_a_timer()
    {
        $this->assertNotFalse(Timer::start());
    }

    /**
     * @test
     * @group timerServicesApiCalls
     */
    public function it_can_know_if_a_timer_is_currently_running()
    {
        $this->assertFalse(Timer::running());
        $this->assertNotFalse(Timer::start());

        $this->assertNotFalse(Timer::running());
    }

    /**
     * @test
     * @group timerServicesApiCalls
     */
    public function it_can_stop_a_timer()
    {
        $timerId = Timer::start();

        $this->assertNotFalse(Timer::running());
        $this->assertTrue(Timer::stop());
        $this->assertFalse(Timer::running());

        Workflow::serviceEnabled()->deleteTimer($timerId);
    }

    /**
     * @test
     * @group timerServicesApiCalls
     */
    public function it_can_continue_a_timer()
    {
        $timerId = Timer::start();
        $this->assertTrue(Timer::stop());

        $this->assertNotFalse(Timer::continue($timerId));

        Workflow::serviceEnabled()->deleteTimer($timerId);
    }
}
