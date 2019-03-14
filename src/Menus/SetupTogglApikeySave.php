<?php

namespace Godbout\Alfred\Time\Menus;

use Godbout\Alfred\Time\Workflow;
use Godbout\Alfred\Workflow\Icon;
use Godbout\Alfred\Workflow\Item;
use Godbout\Alfred\Workflow\ScriptFilter;

class SetupTogglApikeySave extends Menu
{
    public static function scriptFilter()
    {
        self::saveApikey();

        ScriptFilter::add(
            self::apikeySaved(),
            self::back()
        );
    }

    private static function saveApikey()
    {
        Workflow::getConfig()->write('toggl.api_token', getenv('toggl_apikey'));
    }

    private static function apikeySaved()
    {
        return Item::create()
            ->title('API KEY SAVED!')
            ->subtitle('You can just press Enter.')
            ->arg('notification')
            ->icon(Icon::create('resources/icons/toggl.png'));
    }

    private static function back()
    {
        return Item::create()
            ->title('Back')
            ->subtitle('Go back to Toggl Setup')
            ->arg('setup_toggl')
            ->icon(Icon::create('resources/icons/toggl.png'));
    }
}
