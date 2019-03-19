<?php

namespace Tests\Unit;

use Tests\TestCase;
use Godbout\Alfred\Time\Harvest;

class HarvestTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->harvest = new Harvest(getenv('HARVEST_ACCOUNT_ID'), getenv('HARVEST_APIKEY'));
    }

    // public function tearDown(): void
    // {
    //     parent::tearDown();

    //     if ($timerId = $this->toggl->runningTimer()) {
    //         $this->toggl->stopCurrentTimer();
    //         $this->toggl->deleteTimer($timerId);
    //     }
    // }

    /** @test */
    public function it_returns_zero_project_if_the_service_cannot_authenticate()
    {
        $harvest = new Harvest('wrong account', 'wrong token');

        $this->assertEmpty($harvest->projects());
    }

    /**
     * @test
     * @group timerServicesApiCalls
     */
    public function it_returns_projects_if_the_service_can_authenticate()
    {
        $projects = $this->harvest->projects();

        $this->assertArrayHasKey(getenv('HARVEST_PROJECT_ID'), $projects);
        $this->assertSame(getenv('HARVEST_PROJECT_NAME'), $projects[getenv('HARVEST_PROJECT_ID')]);
    }

    /** @test */
    public function it_returns_zero_tag_if_the_service_annot_authenticate()
    {
        $harvest = new Harvest('wrong account', 'wrong token');

        $this->assertEmpty($harvest->tags());
    }

    /**
     * @test
     * @group timerServicesApiCalls
     */
    public function it_returns_tags_if_the_service_can_authenticate()
    {
        $tags = $this->harvest->tags();

        $this->assertArrayHasKey(getenv('HARVEST_TAG_ID'), $tags);
        $this->assertSame(getenv('HARVEST_TAG_NAME'), $tags[getenv('HARVEST_TAG_ID')]);
    }

    /**
     * @test
     * @group timerServicesApiCalls
     */
    public function it_can_start_a_timer()
    {
        putenv('timer_description=description');
        putenv('timer_project=' . getenv('HARVEST_PROJECT_ID'));
        putenv('timer_tag=' . getenv('HARVEST_TAG_ID'));

        $this->assertNotFalse($this->harvest->startTimer());
    }

    /**
     * @test
     * group timerServicesApiCalls
     */
    public function it_can_stop_a_timer()
    {
        putenv('timer_description=description');
        putenv('timer_project=' . getenv('HARVEST_PROJECT_ID'));
        putenv('timer_tag=' . getenv('HARVEST_TAG_ID'));

        $this->assertFalse($this->harvest->stopCurrentTimer());

        $timerId = $this->harvest->startTimer();
        $this->assertTrue($this->harvest->stopCurrentTimer());

        $this->harvest->deleteTimer($timerId);
    }

    /**
     * @test
     * group timerServicesApiCalls
     */
    public function it_can_get_the_running_timer()
    {
        $this->assertFalse($this->harvest->runningTimer());

        $timerId = $this->harvest->startTimer();
        $this->assertNotFalse($this->harvest->runningTimer());
    }

    // /**
    //  * @test
    //  * @group timerServicesApiCalls
    //  */
    // public function it_can_delete_a_timer()
    // {
    //     $timerId = $this->toggl->startTimer();

    //     $this->assertTrue($this->toggl->deleteTimer($timerId));
    // }

    /** @test */
    public function an_Harvest_object_returns_harvest_as_a_string()
    {
        $this->assertSame('harvest', (string) $this->harvest);
    }

    /** @test */
    public function it_does_not_allow_empty_project_for_timer()
    {
        $this->assertFalse($this->harvest->allowsEmptyProject);
    }
}
