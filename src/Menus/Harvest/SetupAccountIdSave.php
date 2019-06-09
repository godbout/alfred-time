<?php

namespace Godbout\Alfred\Time\Menus\Harvest;

use Godbout\Alfred\Time\Workflow;
use Godbout\Alfred\Workflow\Icon;
use Godbout\Alfred\Workflow\Item;
use Godbout\Alfred\Time\Menus\Menu;
use Godbout\Alfred\Workflow\ScriptFilter;

class SetupAccountIdSave extends Menu
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
            ->icon(Icon::create('resources/icons/harvest.png'))
            ->arg('do')
            ->variable('timer_action', 'exit');
    }

    private static function back()
    {
        return Item::create()
            ->title('Back')
            ->subtitle('Go back to Harvest credentials options')
            ->arg('harvest_setup_credentials')
            ->icon(Icon::create('resources/icons/harvest.png'));
    }
}
