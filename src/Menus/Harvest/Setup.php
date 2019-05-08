<?php

namespace Godbout\Alfred\Time\Menus\Harvest;

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
            self::credentials(),
            self::state(),
            self::back()
        );
    }

    private static function credentials()
    {
        return Item::create()
            ->title(self::credentialsTitle())
            ->subtitle('API token and Account ID')
            ->arg('harvest_setup_credentials')
            ->icon(Icon::create('resources/icons/harvest.png'));
    }

    private static function credentialsTitle()
    {
        return self::credentialsFound() ? 'Update credentials' : 'Set credentials';
    }

    private static function credentialsFound()
    {
        return (
            ! empty(Workflow::getConfig()->read('harvest.api_token'))
            || ! empty(Workflow::getConfig()->read('harvest.account_id'))
        );
    }

    private static function state()
    {
        return Item::create()
            ->title(self::stateTitle())
            ->subtitle(self::stateSubtitle())
            ->arg('harvest_setup_state')
            ->variable('harvest_enabled', Workflow::getConfig()->read('harvest.is_active') ? 'false' : 'true')
            ->icon(Icon::create('resources/icons/harvest.png'));
    }

    private static function stateTitle()
    {
        return (Workflow::getConfig()->read('harvest.is_active') === true) ? 'Disable' : 'Enable';
    }

    private static function stateSubtitle()
    {
        return (Workflow::getConfig()->read('harvest.is_active') === true) ? 'Currently enabled' : 'Currently disabled';
    }

    private static function back()
    {
        return Item::create()
            ->title('Back')
            ->arg('setup')
            ->icon(Icon::create('resources/icons/icon.png'));
    }
}
