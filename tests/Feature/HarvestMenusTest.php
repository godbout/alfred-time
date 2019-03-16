<?php

namespace Tests\Feature;

use Tests\TestCase;

class HarvestMenusTest extends TestCase
{
    /** @test */
    public function it_shows_setting_up_credentials_as_a_menu_option()
    {
        $output = $this->reachHarvestSetupMenu();

        $this->assertStringContainsString('"title":"Set credentials"', $output);
    }

    /** @test */
    public function it_can_enable_harvest()
    {
        $output = $this->reachHarvestStateSavedMenu('harvest_enabled=true');

        $fileContentAsArray = json_decode(file_get_contents($this->configFile), true);
        $this->assertArrayHasKey('is_active', $fileContentAsArray['harvest']);
        $this->assertSame(true, $fileContentAsArray['harvest']['is_active']);
    }

    /** @test */
    public function it_can_disable_harvest()
    {
        putenv('harvest_enabled=false');

        $output = $this->reachHarvestStateSavedMenu();

        $fileContentAsArray = json_decode(file_get_contents($this->configFile), true);
        $this->assertArrayHasKey('is_active', $fileContentAsArray['harvest']);
        $this->assertSame(false, $fileContentAsArray['harvest']['is_active']);
    }

    /** @test */
    public function it_can_save_the_api_key_of_the_user_in_the_config_file()
    {
        $apiToken = '1153865.pt.Vjxherj4YPfPiEhTp3jORa3OZYIK15VD2wkAPmrA1Y7uOBUzsi-WtFznKGxJIuc2rnnFDxWV-lj946fGI42hNQ';

        $output = $this->reachHarvestApitokenSavedMenu("harvest_apitoken=$apiToken");

        $fileContentAsArray = json_decode(file_get_contents($this->configFile), true);
        $this->assertArrayHasKey('api_token', $fileContentAsArray['harvest']);
        $this->assertSame($apiToken, $fileContentAsArray['harvest']['api_token']);
    }

    /** @test */
    public function it_shows_creating_an_api_token_if_none_is_saved_in_the_config_yet()
    {
        $this->markTestSkipped('Changes in menu for the time being.');

        $this->harvestApitoken('');

        $output = $this->reachHarvestSetupMenu();

        $this->assertStringContainsString('"subtitle":"No API token found"', $output);
    }
}
