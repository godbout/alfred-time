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
     * @group default
     */
    public function it_returns_projects_if_the_service_can_authenticate()
    {
        $this->enableToggl();
        $this->togglApikey(getenv('TOGGL_APIKEY'));

        $projects = Workflow::serviceEnabled()->projects();

        $this->assertArrayHasKey(getenv('TOGGL_PROJECT_ID'), $projects);
        $this->assertSame(getenv('TOGGL_PROJECT_NAME'), $projects[getenv('TOGGL_PROJECT_ID')]);
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

        $this->assertArrayHasKey(getenv('TOGGL_TAG_ID'), $tags);
        $this->assertSame(getenv('TOGGL_TAG_NAME'), $tags[getenv('TOGGL_TAG_ID')]);
    }

    /** @test */
    public function it_does_not_show_projects_that_have_been_deleted_serverwise()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function it_does_not_show_tags_that_have_been_deleted_serverwise()
    {
        $this->markTestIncomplete();
    }

    /**
     * @test
     * @group timerServicesApiCalls
     */
    public function it_can_start_a_timer()
    {
        /**
         * iTodo
         *
         * - Fails. Probably because of tag (when empty)
         */
        $this->markTestSkipped();
        $this->enableToggl();
        $this->togglApikey(getenv('TOGGL_APIKEY'));

        $output = Workflow::serviceEnabled()->startTimer();

        $this->assertTrue($output);
    }

    /**
     * @test
     * @group timerServicesApiCalls
     * @depends it_can_start_a_timer
     */
    public function it_can_stop_a_timer()
    {
        $this->markTestIncomplete();
    }
}
