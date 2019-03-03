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

        $this->assertEmpty($service->projects());
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

    /** @test */
    public function it_returns_zero_tag_if_the_service_annot_authenticate()
    {
        $this->enableToggl();
        $this->togglApikey('wrong apikey');

        $service = Workflow::serviceEnabled();

        $this->assertEmpty($service->tags());
    }

    /**
     * @test
     * @group timerServicesApiCalls
     */
    public function it_returns_tags_if_the_service_can_authenticate()
    {
        $this->enableToggl();
        $this->togglApikey(getenv('TOGGL_APIKEY'));

        $tags = Workflow::serviceEnabled()->tags();

        $this->assertArrayHasKey(2755832, $tags);
        $this->assertSame('All Included Package', $tags[2755832]);
    }

    /** @test */
    public function it_does_not_show_projects_that_have_been_deleted_serverwise()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function it_does_not_show_tags_that_havbe_been_deleted_serverwise()
    {
        $this->markTestIncomplete();
    }
}
