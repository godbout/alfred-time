<?php

namespace Tests\Feature;

use Godbout\Alfred\Time\Workflow;
use Tests\TestCase;

class HarvestSetupMenusTest extends TestCase
{
    /** @test */
    public function it_proposes_to_enter_service_options_if_service_setup_is_chosen_and_offers_a_go_back_option()
    {
        $output = $this->reachHarvestSetupMenu();

        $this->assertStringContainsString('harvest_setup_credentials"', $output);
        $this->assertStringContainsString('harvest_setup_state"', $output);
        $this->assertStringContainsString('"setup"', $output);
    }

    /** @test */
    public function it_shows_setting_up_credentials_as_a_menu_option()
    {
        $this->harvestApitoken('');
        $this->harvestAccountId('');

        $output = $this->reachHarvestSetupMenu();

        $this->assertStringContainsString('"title":"Set credentials"', $output);
    }

    /** @test */
    public function it_shows_updating_credentials_if_some_are_found_in_the_config()
    {
        $this->harvestApitoken('234kjh2kjhfkajdsf');
        $this->harvestAccountId('');

        $output = $this->reachHarvestSetupMenu();

        $this->assertStringContainsString('"title":"Update credentials"', $output);


        $this->harvestApitoken('');
        $this->harvestAccountId('987654');

        $output = $this->reachHarvestSetupMenu();

        $this->assertStringContainsString('"title":"Update credentials"', $output);
    }

    /** @test */
    public function it_shows_creating_an_api_token_if_none_is_saved_in_the_config_yet()
    {
        $this->harvestApitoken('');

        $output = $this->reachHarvestCredentialsSetupMenu();

        $this->assertStringContainsString('"subtitle":"No API token found"', $output);
    }

    /** @test */
    public function it_shows_updating_an_api_token_is_one_is_found_in_the_config()
    {
        $this->harvestApitoken('1153865.pt.Vjxherj4YPfPiEhTp3jORa3OZYIK15VD2wkAPmrA1Y7uOBUzsi-WtFznKGxJIuc2rnnFDxWV-lj946fGI42hNQ');

        $output = $this->reachHarvestCredentialsSetupMenu();

        $this->assertStringContainsString('"subtitle":"Current API token: 1153865.pt...."', $output);
    }

    /** @test */
    public function it_can_save_the_apitoken_of_the_user_in_the_config_file()
    {
        $apiToken = '1153865.pt.Vjxherj4YPfPiEhTp3jORa3OZYIK15VD2wkAPmrA1Y7uOBUzsi-WtFznKGxJIuc2rnnFDxWV-lj946fGI42hNQ';

        $output = $this->reachHarvestApitokenSavedMenu("harvest_apitoken=$apiToken");

        $fileContentAsArray = json_decode(file_get_contents($this->configFile), true);
        $this->assertArrayHasKey('api_token', $fileContentAsArray['harvest']);
        $this->assertSame($apiToken, $fileContentAsArray['harvest']['api_token']);
    }

    /** @test */
    public function it_can_save_the_account_id_of_the_user_in_the_config_file()
    {
        $accountId = '987654';

        $output = $this->reachHarvestAccountIdSavedMenu("harvest_account_id=$accountId");

        $fileContentAsArray = json_decode(file_get_contents($this->configFile), true);
        $this->assertArrayHasKey('account_id', $fileContentAsArray['harvest']);
        $this->assertSame($accountId, $fileContentAsArray['harvest']['account_id']);
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
    public function it_allows_to_quit_the_workflow_after_apitoken_is_saved()
    {
        Workflow::enableService('harvest');

        $output = $this->reachHarvestApitokenSavedMenu();

        $this->assertStringContainsString('"arg":"do"', $output);
        $this->assertStringContainsString('"action":"exit"', $output);
    }

    /** @test */
    public function it_allows_to_quit_the_workflow_after_account_id_is_saved()
    {
        Workflow::enableService('harvest');

        $output = $this->reachHarvestAccountIdSavedMenu();

        $this->assertStringContainsString('"arg":"do"', $output);
        $this->assertStringContainsString('"action":"exit"', $output);
    }

    /** @test */
    public function it_allows_to_quit_the_workflow_after_status_is_changed()
    {
        Workflow::enableService('harvest');

        $output = $this->reachHarvestStateSavedMenu();

        $this->assertStringContainsString('"arg":"do"', $output);
        $this->assertStringContainsString('"action":"exit"', $output);
    }
}
