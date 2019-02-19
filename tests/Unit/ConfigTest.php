<?php

namespace Tests\Unit;

use Tests\TestCase;
use Godbout\Time\Config;

class ConfigTest extends TestCase
{
    /** @test */
    public function it_can_write_the_config_to_the_alfred_workflow_data_folder()
    {
        $config = [
            'timer' => [
                'primary_service' => 'harvest',
                'harvest_id' => null,
            ],
            'toggl' => [
                'is_active' => true,
            ],
            'harvest' => [
                'domain' => '',
                'api_token' => '',
            ],
        ];

        Config::writeToFile($this->configFile, $config);

        $this->assertSame($config, json_decode(file_get_contents($this->configFile), true));
    }
}
