<?php

namespace Tests\Unit;

use Godbout\Alfred\Time\Services\Clockify;
use Godbout\Alfred\Time\Workflow;
use Tests\TestCase;

class ClockifyTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->clockify = new Clockify(getenv('CLOCKIFY_APIKEY'));

        Workflow::enableService('clockify');

        $this->setClockifyTimerAttributes();

        sleep(4);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        if ($timerId = $this->clockify->runningTimer()) {
            $this->clockify->stopCurrentTimer();
            $this->clockify->deleteTimer($timerId);
        }
    }

    /** @test */
    public function it_returns_zero_project_if_the_service_cannot_authenticate()
    {
        $clockify = new Clockify('wrong apikey');

        $this->assertEmpty($clockify->projects());
    }
}
