<?php

namespace Godbout\Time\Menus;

use Godbout\Alfred\Icon;
use Godbout\Alfred\Item;
use Godbout\Alfred\ScriptFilter;

class None
{
    public static function content()
    {
        self::generateDefaultConfigFileIfNonExistent();

        ScriptFilter::add(
            Item::create()
                ->title('Setup the workflow')
                ->arg('setup')
                ->icon(
                    Icon::create(__DIR__ . '/../../resources/icons/icon.png')
                )
        );
    }

    private static function generateDefaultConfigFileIfNonExistent()
    {
        $defaultConfig = [
            'timer' => [
                'primary_service' => 'toggl',
                'is_running' => false,
                'toggl_id' => null,
                'harvest_id' => null,
                'description' => '',
            ],
            'toggl' => [
                'is_active' => true,
                'api_token' => '',
            ],
            'harvest' => [
                'is_active' => false,
                'domain' => '',
                'api_token' => '',
            ],
        ];

        if (! file_exists(getenv('alfred_workflow_data'))) {
            mkdir(getenv('alfred_workflow_data'));
        }

        if (! file_exists(getenv('alfred_workflow_data') . '/config.json')) {
            file_put_contents(
                getenv('alfred_workflow_data') . '/config.json',
                json_encode($defaultConfig, JSON_PRETTY_PRINT)
            );
        }
    }
}
