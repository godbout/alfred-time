<?php

namespace Godbout\Alfred\Time\Menus;

use Godbout\Alfred\Time\Workflow;
use Godbout\Alfred\Workflow\Icon;
use Godbout\Alfred\Workflow\Item;
use Godbout\Alfred\Workflow\ScriptFilter;

class SetupHarvest extends Menu
{
    public static function scriptFilter()
    {
        ScriptFilter::add(
            // self::apitoken(),
            self::credentials(),
            self::state(),
            self::back()
        );
    }

    private static function credentials()
    {
        return Item::create()
            ->title('Set credentials')
            ->subtitle('API token and Account ID')
            ->arg('setup_harvest_credentials')
            ->icon(Icon::create('resources/icons/harvest.png'));
    }



    // private static function apitoken()
    // {
    //     return Item::create()
    //         ->title(self::apitokenTitle())
    //         ->subtitle(self::apitokenSubtitle())
    //         ->arg('setup_harvest_apitoken')
    //         ->icon(Icon::create('resources/icons/harvest.png'));
    // }

    // private static function apitokenTitle()
    // {
    //     return empty(Workflow::getConfig()->read('harvest.api_token')) ? 'Set API token' : 'Update API token';
    // }

    // private static function apitokenSubtitle()
    // {
    //     $apitoken = Workflow::getConfig()->read('harvest.api_token');

    //     return empty($apitoken) ? 'No API token found' : 'Current API token: ' . substr($token, 0, 11) . '...';
    // }

    private static function state()
    {
        return Item::create()
            ->title(self::stateTitle())
            ->subtitle(self::stateSubtitle())
            ->arg('setup_harvest_state')
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
