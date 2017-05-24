<?php

namespace AlfredTime;

require_once __DIR__ . '/../../src/Config.php';
use \Codeception\Util\Stub;

class ConfigTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function testActivatedServices()
    {
        $config = Stub::make(
            'AlfredTime\Config', [
                'config' => [
                    'toggl'   => ['is_active' => false],
                    'harvest' => ['is_active' => false],
                ],
            ]
        );

        $services = $config->activatedServices();
        $this->assertEquals(0, count($services));

        $config = Stub::make(
            'AlfredTime\Config', [
                'config' => [
                    'toggl'   => ['is_active' => true],
                    'harvest' => ['is_active' => false],
                ],
            ]
        );

        $services = $config->activatedServices();
        $this->assertEquals(1, count($services));
        $this->assertContains('toggl', $services);

        $config = Stub::make(
            'AlfredTime\Config', [
                'config' => [
                    'toggl'   => ['is_active' => false],
                    'harvest' => ['is_active' => true],
                ],
            ]
        );

        $services = $config->activatedServices();
        $this->assertEquals(1, count($services));
        $this->assertContains('harvest', $services);

        $config = Stub::make(
            'AlfredTime\Config', [
                'config' => [
                    'toggl'   => ['is_active' => true],
                    'harvest' => ['is_active' => true],
                ],
            ]
        );

        $services = $config->activatedServices();
        $this->assertEquals(2, count($services));
        $this->assertContains('toggl', $services);
        $this->assertContains('harvest', $services);
    }

    public function testRunningServices()
    {
        $config = Stub::make(
            'AlfredTime\Config', [
                'config' => [
                    'timer'   => ['toggl_id' => null, 'harvest_id' => null],
                    'toggl'   => ['is_active' => false],
                    'harvest' => ['is_active' => false],
                ],
            ]
        );

        $services = $config->runningServices();
        $this->assertEquals(0, count($services));

        $config = Stub::make(
            'AlfredTime\Config', [
                'config' => [
                    'timer'   => ['toggl_id' => 234, 'harvest_id' => 2342],
                    'toggl'   => ['is_active' => false],
                    'harvest' => ['is_active' => false],
                ],
            ]
        );
        $services = $config->runningServices();
        $this->assertEquals(0, count($services));

        $config = Stub::make(
            'AlfredTime\Config', [
                'config' => [
                    'timer'   => ['toggl_id' => 234, 'harvest_id' => 2342],
                    'toggl'   => ['is_active' => true],
                    'harvest' => ['is_active' => false],
                ],
            ]
        );
        $services = $config->runningServices();
        $this->assertEquals(1, count($services));
        $this->assertContains('toggl', $services);

        $config = Stub::make(
            'AlfredTime\Config', [
                'config' => [
                    'timer'   => ['toggl_id' => null, 'harvest_id' => null],
                    'toggl'   => ['is_active' => true],
                    'harvest' => ['is_active' => true],
                ],
            ]
        );
        $services = $config->runningServices();
        $this->assertEquals(0, count($services));

        $config = Stub::make(
            'AlfredTime\Config', [
                'config' => [
                    'timer'   => ['toggl_id' => 234, 'harvest_id' => 2342],
                    'toggl'   => ['is_active' => true],
                    'harvest' => ['is_active' => true],
                ],
            ]
        );
        $services = $config->runningServices();
        $this->assertEquals(2, count($services));
        $this->assertContains('toggl', $services);
        $this->assertContains('harvest', $services);
    }
}
