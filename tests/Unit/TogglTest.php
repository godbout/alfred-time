<?php

namespace Tests\Unit;

use Tests\TestCase;
use Godbout\Alfred\Time\Toggl;

class TogglTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->toggl = new Toggl(getenv('TOGGL_APIKEY'));
    }

    public function tearDown(): void
    {
        parent::tearDown();

        if ($timerId = $this->toggl->runningTimer()) {
            $this->toggl->stopCurrentTimer();
            $this->toggl->deleteTimer($timerId);
        }
    }

    /** @test */
    public function it_returns_zero_project_if_the_service_cannot_authenticate()
    {
        $toggl = new Toggl('wrong apikey');

        $this->assertEmpty($toggl->projects());
    }

    /**
     * @test
     * @group timerServicesApiCalls
     */
    public function it_returns_projects_if_the_service_can_authenticate()
    {
        $projects = $this->toggl->projects();

        $this->assertArrayHasKey(getenv('TOGGL_PROJECT_ID'), $projects);
        $this->assertSame(getenv('TOGGL_PROJECT_NAME'), $projects[getenv('TOGGL_PROJECT_ID')]);
    }

    /** @test */
    public function it_returns_zero_tag_if_the_service_annot_authenticate()
    {
        $toggl = new Toggl('wrong apikey again');

        $this->assertEmpty($toggl->tags());
    }

    /**
     * @test
     * @group timerServicesApiCalls
     */
    public function it_returns_tags_if_the_service_can_authenticate()
    {
        $tags = $this->toggl->tags();

        $this->assertArrayHasKey(getenv('TOGGL_TAG_ID'), $tags);
        $this->assertSame(getenv('TOGGL_TAG_NAME'), $tags[getenv('TOGGL_TAG_ID')]);
    }

    /**
     * @test
     * @group timerServicesApiCalls
     */
    public function it_does_not_show_projects_that_have_been_deleted_serverwise()
    {
        $projects = $this->toggl->projects();

        $this->assertArrayNotHasKey(getenv('TOGGL_DELETED_PROJECT_ID'), $projects);
        $this->assertNotContains(getenv('TOGGL_DELETED_PROJECT_NAME'), $projects);
    }

    /**
     * @test
     * @group timerServicesApiCalls
     */
    public function it_can_start_a_timer()
    {
        $this->assertNotFalse($this->toggl->startTimer());
    }

    /**
     * @test
     * @group timerServicesApiCalls
     */
    public function it_can_stop_a_timer()
    {
        $this->assertFalse($this->toggl->stopCurrentTimer());

        $timerId = $this->toggl->startTimer();
        $this->assertTrue($this->toggl->stopCurrentTimer());

        $this->toggl->deleteTimer($timerId);
    }

    /**
     * @test
     * @group timerServicesApiCalls
     */
    public function it_can_get_the_running_timer()
    {
        $this->assertFalse($this->toggl->runningTimer());

        $timerId = $this->toggl->startTimer();
        $this->assertNotFalse($this->toggl->runningTimer());
    }

    /**
     * @test
     * @group timerServicesApiCalls
     */
    public function it_can_delete_a_timer()
    {
        $timerId = $this->toggl->startTimer();

        $this->assertTrue($this->toggl->deleteTimer($timerId));
    }

    /** @test */
    public function an_Toggl_object_returns_toggl_as_a_string()
    {
        $this->assertSame('toggl', (string) $this->toggl);
    }

    /** @test */
    public function it_allows_empty_project_for_timer()
    {
        $this->assertTrue($this->toggl->allowsEmptyProject);
    }
}
