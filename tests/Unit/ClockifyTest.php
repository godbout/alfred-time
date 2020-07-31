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

        sleep(3);
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

    /**
     * @test
     * @group timerServicesApiCalls
     */
    public function it_returns_projects_if_the_service_can_authenticate()
    {
        $projects = $this->clockify->projects();

        $this->assertArrayHasKey(getenv('CLOCKIFY_PROJECT_ID'), $projects);
        $this->assertSame(getenv('CLOCKIFY_PROJECT_NAME'), $projects[getenv('CLOCKIFY_PROJECT_ID')]);
    }

    /** @test */
    public function it_returns_zero_tag_if_the_service_cannot_authenticate()
    {
        $clockify = new Clockify('wrong apikey again');

        $this->assertEmpty($clockify->tags());
    }

    /**
     * @test
     * @group timerServicesApiCalls
     */
    public function it_returns_tags_if_the_service_can_authenticate()
    {
        $tags = $this->clockify->tags();

        $this->assertArrayHasKey(getenv('CLOCKIFY_TAG_ID'), $tags);
        $this->assertSame(getenv('CLOCKIFY_TAG_NAME'), $tags[getenv('CLOCKIFY_TAG_ID')]);
    }

    /**
     * @test
     * @group timerServicesApiCalls
     */
    public function it_can_start_a_timer()
    {
        $this->assertNotFalse($this->clockify->startTimer());

        $this->clockify->stopCurrentTimer();
    }

    /**
     * @test
     * @group timerServicesApiCalls
     */
    public function it_can_stop_a_timer()
    {
        $this->assertFalse($this->clockify->stopCurrentTimer());

        $this->clockify->startTimer();
        $this->assertTrue($this->clockify->stopCurrentTimer());
    }

    /**
     * @test
     * @group timerServicesApiCalls
     */
    public function it_can_get_the_running_timer()
    {
        $this->assertFalse($this->clockify->runningTimer());

        $timerId = $this->clockify->startTimer();
        $this->assertNotFalse($this->clockify->runningTimer());

        $this->clockify->stopCurrentTimer();
    }

    /**
     * @test
     * @group timerServicesApiCalls
     */
    public function it_can_return_the_list_of_past_timers()
    {
        $this->clockify->startTimer();
        sleep(2);
        $this->clockify->stopCurrentTimer();

        $latestTimer = $this->clockify->pastTimers()[0];

        $this->assertNotNull($latestTimer->id);
        $this->assertObjectHasAttribute('description', $latestTimer);
        $this->assertObjectHasAttribute('duration', $latestTimer);
    }

    /**
     * @test
     * @group timerServicesApiCalls
     */
    public function it_can_continue_a_timer()
    {
        $timerId = $this->clockify->startTimer();

        $this->assertTrue($this->clockify->stopCurrentTimer());
        $this->assertFalse($this->clockify->runningTimer());

        $restartedTimerId = $this->clockify->continueTimer();

        $this->assertNotFalse($restartedTimerId);
        $this->assertSame($restartedTimerId, $this->clockify->runningTimer());
    }

    /** @test */
    public function a_Clockify_object_returns_clockify_as_a_string()
    {
        $this->assertSame('clockify', (string) $this->clockify);
    }

    /** @test */
    public function it_allows_empty_project_for_timer()
    {
        $this->assertTrue($this->clockify->allowsEmptyProject);
    }
}
