<?php

namespace Godbout\Alfred\Time\Menus\Everhour;

use Godbout\Alfred\Time\Workflow;
use Godbout\Alfred\Workflow\Icon;
use Godbout\Alfred\Workflow\Item;
use Godbout\Alfred\Time\Menus\Menu;
use Godbout\Alfred\Workflow\ScriptFilter;

class Setup extends Menu
{
    public static function scriptFilter()
    {
        ScriptFilter::add(
            self::apikey(),
            self::state(),
            self::back()
        );
    }

    private static function apikey()
    {
        return Item::create()
            ->title(self::apikeyTitle())
            ->subtitle(self::apikeySubtitle())
            ->arg('everhour_setup_apikey')
            ->icon(Icon::create('resources/icons/everhour.png'));
    }

    private static function apikeyTitle()
    {
        return empty(Workflow::getConfig()->read('everhour.api_token')) ? 'Set API KEY' : 'Update API KEY';
    }

    private static function apikeySubtitle()
    {
        $apikey = Workflow::getConfig()->read('everhour.api_token');

        return empty($apikey) ? 'No API KEY found' : 'Current API KEY: ' . substr($apikey, 0, 11) . '...';
    }

    private static function state()
    {
        return Item::create()
            ->title(self::stateTitle())
            ->subtitle(self::stateSubtitle())
            ->arg('everhour_setup_state')
            ->variable('everhour_enabled', Workflow::getConfig()->read('everhour.is_active') ? 'false' : 'true')
            ->icon(Icon::create('resources/icons/everhour.png'));
    }

    private static function stateTitle()
    {
        return (Workflow::getConfig()->read('everhour.is_active') === true) ? 'Disable' : 'Enable';
    }

    private static function stateSubtitle()
    {
        return (Workflow::getConfig()->read('everhour.is_active') === true) ? 'Currently enabled' : 'Currently disabled';
    }

    private static function back()
    {
        return Item::create()
            ->title('Back')
            ->arg('setup')
            ->icon(Icon::create('resources/icons/icon.png'));
    }
}
