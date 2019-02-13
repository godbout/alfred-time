<?php

namespace Tests;

class WorkflowTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_proposes_a_setup_if_the_workflow_is_not_yet_configured()
    {
        unlink($this->configFile);

        $output = $this->mockAlfredCallToScriptFilter();

        $this->assertStringContainsString('"arg":"setup"', $output);
    }

    /** @test */
    public function it_proposes_to_setup_toggl_and_harvest_if_setup_is_accepted()
    {
        putenv("action=setup");

        $output = $this->mockAlfredCallToScriptFilter();

        $this->assertStringContainsString('setup_toggl', $output);
        $this->assertStringContainsString('setup_harvest', $output);
    }

    /** @test */
    public function it_proposes_to_enter_toggl_options_if_toggl_setup_is_chosen()
    {
        putenv("action=setup_toggl");

        $output = $this->mockAlfredCallToScriptFilter();

        $this->assertStringContainsString('setup_toggl_apikey', $output);
        $this->assertStringContainsString('setup_toggl_state', $output);
    }
}
