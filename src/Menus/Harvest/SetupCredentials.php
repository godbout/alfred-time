<?php

namespace Godbout\Alfred\Time\Menus\Harvest;

use Godbout\Alfred\Time\Workflow;
use Godbout\Alfred\Workflow\Icon;
use Godbout\Alfred\Workflow\Item;
use Godbout\Alfred\Time\Menus\Menu;
use Godbout\Alfred\Workflow\ScriptFilter;

class SetupCredentials extends Menu
{
    public static function scriptFilter()
    {
        ScriptFilter::add(
            self::accountId(),
            self::apitoken(),
            self::back()
        );
    }

    private static function apitoken()
    {
        return Item::create()
            ->title(self::apitokenTitle())
            ->subtitle(self::apitokenSubtitle())
            ->arg('harvest_setup_apitoken')
            ->icon(Icon::create('resources/icons/harvest.png'));
    }

    private static function accountId()
    {
        return Item::create()
            ->title(self::accountIdTitle())
            ->subtitle(self::accountIdSubtitle())
            ->arg('harvest_setup_account_id')
            ->icon(Icon::create('resources/icons/harvest.png'));
    }

    private static function apitokenTitle()
    {
        return empty(Workflow::getConfig()->read('harvest.api_token')) ? 'Set API token' : 'Update API token';
    }

    private static function apitokenSubtitle()
    {
        $apitoken = Workflow::getConfig()->read('harvest.api_token');

        return empty($apitoken) ? 'No API token found' : 'Current API token: ' . substr($apitoken, 0, 11) . '...';
    }

    private static function accountIdTitle()
    {
        return empty(Workflow::getConfig()->read('harvest.account_id')) ? 'Set Account ID' : 'Update Account ID';
    }

    private static function accountIdSubtitle()
    {
        $accountId = Workflow::getConfig()->read('harvest.account_id');

        return empty($accountId) ? 'No Account ID found' : 'Current Account ID: ' . substr($accountId, 0, 4) . '...';
    }

    private static function back()
    {
        return Item::create()
            ->title('Back')
            ->arg('harvest_setup')
            ->icon(Icon::create('resources/icons/harvest.png'));
    }
}
