<?php

namespace Tests\Feature;

use Tests\TestCase;
use Godbout\Alfred\Time\Workflow;

class EverhourSetupMenusTest extends TestCase
{
    /** @test */
    public function it_shows_setting_an_api_key_if_none_is_saved_in_the_config_yet()
    {
        $this->everhourApikey('');

        $output = $this->reachEverhourSetupMenu();

        $this->assertStringContainsString('"subtitle":"No API KEY found"', $output);
    }

    /** @test */
    public function some_more_test()
    {
        $this->markTestIncomplete('lots of test to add for everhour');
    }
}
