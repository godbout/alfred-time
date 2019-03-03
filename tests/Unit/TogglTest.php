<?php

namespace Tests\Unit;

use Tests\TestCase;
use Godbout\Alfred\Time\Workflow;

class TogglTest extends TestCase
{
    /** @test */
    public function it_returns_zero_project_if_the_service_cannot_authenticate()
    {
        $this->enableToggl();
        $this->togglApikey('wrong apikey');

        $service = Workflow::serviceEnabled();

        $this->assertSame([], $service->projects());
    }

    /**
     * @test
     * @group timerServicesApiCalls
     */
    public function it_returns_projects_if_the_service_can_authenticate()
    {
        $this->enableToggl();
        $this->togglApikey(getenv('TOGGL_APIKEY'));

        $projects = Workflow::serviceEnabled()->projects();

        $this->assertArrayHasKey(35673866, $projects);
        $this->assertSame('Alfred-Time', $projects[35673866]);
    }
}
