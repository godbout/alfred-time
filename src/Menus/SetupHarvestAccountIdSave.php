<?php

namespace Godbout\Alfred\Time\Menus;

use Godbout\Alfred\Time\Workflow;
use Godbout\Alfred\Workflow\Icon;
use Godbout\Alfred\Workflow\Item;
use Godbout\Alfred\Workflow\ScriptFilter;

class SetupHarvestAccountIdSave extends Menu
{
    public static function scriptFilter()
    {
        self::saveAccountId();

        ScriptFilter::add(
            self::accountIdSaved(),
            self::back()
        );
    }

    private static function saveAccountId()
    {
        Workflow::getConfig()->write('harvest.account_id', getenv('harvest_account_id'));
    }

    private static function accountIdSaved()
    {
        return Item::create()
            ->title('Account ID SAVED!')
            ->subtitle('You can just press Enter.')
            ->arg('notification')
            ->icon(Icon::create('resources/icons/harvest.png'));
    }

    private static function back()
    {
        return Item::create()
            ->title('Back')
            ->subtitle('Go back to Harvest credentials options')
            ->arg('setup_harvest_credentials')
            ->icon(Icon::create('resources/icons/harvest.png'));
    }
}
