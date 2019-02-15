<?php
declare(strict_types=1);

namespace Tests;

class TogglMenusTest extends TestCase
{
    public function tearDown(): void
    {
        parent::tearDown();

        unlink($this->configFile);
    }
    /** @test */
    public function it_can_save_the_api_key_of_the_user_in_the_config_file()
    {
        unlink($this->configFile);
        $apiKey = 'e695b4364ad1ea7200035fec1bbc87cf';
        putenv('action=setup_toggl_apikey_save');
        putenv("toggl_apikey=$apiKey");

        $output = $this->mockAlfredCallToScriptFilter();

        $fileContentAsArray = json_decode(file_get_contents($this->configFile), true);
        $this->assertArrayHasKey('toggl', $fileContentAsArray);
        $this->assertArrayHasKey('api_token', $fileContentAsArray['toggl']);
        $this->assertSame($apiKey, $fileContentAsArray['toggl']['api_token']);
    }
}
