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
    public function it_returns_zero_workspace_if_the_service_cannot_authenticate()
    {
        $clockify = new Clockify('wrong apikey');

        $this->assertEmpty($clockify->workspaces());
    }

    /**
     * @test
     * @group timerServicesApiCalls
     */
    public function it_returns_workspaces_if_the_service_can_authenticate()
    {
        $workspaces = $this->clockify->workspaces();

        $this->assertSame(getenv('CLOCKIFY_WORKSPACE_ID'), $workspaces[0]['id']);
        $this->assertSame(getenv('CLOCKIFY_WORKSPACE_NAME'), $workspaces[0]['name']);
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

        $this->assertSame(getenv('CLOCKIFY_PROJECT_ID'), $projects[0]['id']);
        $this->assertSame(getenv('CLOCKIFY_PROJECT_NAME'), $projects[0]['name']);
    }
}
