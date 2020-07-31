<?php

namespace Tests\Unit;

use Godbout\Alfred\Time\Services\Harvest;
use Godbout\Alfred\Time\Workflow;
use Tests\TestCase;

class HarvestTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->harvest = new Harvest(getenv('HARVEST_ACCOUNT_ID'), getenv('HARVEST_APIKEY'));

        Workflow::enableService('harvest');

        $this->setHarvestTimerAttributes();

        sleep(3);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        if ($timerId = $this->harvest->runningTimer()) {
            $this->harvest->stopCurrentTimer();
            $this->harvest->deleteTimer($timerId);
        }
    }

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
    public function it_returns_only_the_tags_assigned_to_the_specific_project_chosen()
    {
        $tags = $this->harvest->tags();

        $this->assertArrayNotHasKey(getenv('HARVEST_TAG_ID_FROM_OTHER_PROJET'), $tags);
    }

    /**
     * @test
     * @group timerServicesApiCalls
     */
    public function it_can_return_the_list_of_past_timers()
    {
        $timerId = $this->harvest->startTimer();

        $lastestTimer = $this->harvest->pastTimers()[0];

        $this->assertSame($timerId, $lastestTimer->id);
        $this->assertObjectHasAttribute('description', $lastestTimer);
        $this->assertObjectHasAttribute('project_id', $lastestTimer);
        $this->assertObjectHasAttribute('project_name', $lastestTimer);
        $this->assertObjectHasAttribute('tags', $lastestTimer);
        $this->assertObjectHasAttribute('duration', $lastestTimer);
    }

    /**
     * @test
     * @group timerServicesApiCalls
     */
    public function it_can_start_a_timer()
    {
        $this->assertNotFalse($this->harvest->startTimer());
    }

    /**
     * @test
     * @group timerServicesApiCalls
     */
    public function it_can_stop_a_timer()
    {
        $this->assertFalse($this->harvest->stopCurrentTimer());

        $timerId = $this->harvest->startTimer();
        $this->assertTrue($this->harvest->stopCurrentTimer());

        $this->harvest->deleteTimer($timerId);
    }

    /**
     * @test
     * @group timerServicesApiCalls
     */
    public function it_can_get_the_running_timer()
    {
        $this->assertFalse($this->harvest->runningTimer());

        $timerId = $this->harvest->startTimer();
        $this->assertNotFalse($this->harvest->runningTimer());
    }

    /**
     * @test
     * @group timerServicesApiCalls
     */
    public function it_can_continue_a_timer()
    {
        $timerId = $this->harvest->startTimer();

        $this->assertTrue($this->harvest->stopCurrentTimer());

        $restartedTimerId = $this->harvest->continueTimer($timerId);

        $this->assertNotFalse($restartedTimerId);
        $this->assertSame($restartedTimerId, $this->harvest->runningTimer());

        $this->harvest->deleteTimer($timerId);
    }

    /**
     * @test
     * @group timerServicesApiCalls
     */
    public function it_can_delete_a_timer()
    {
        $timerId = $this->harvest->startTimer();

        $this->assertTrue($this->harvest->deleteTimer($timerId));
    }

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
