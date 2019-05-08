<?php

namespace Godbout\Alfred\Time\Menus\Harvest;

use Godbout\Alfred\Workflow\Icon;
use Godbout\Alfred\Workflow\Item;
use Godbout\Alfred\Time\Menus\Menu;
use Godbout\Alfred\Workflow\ScriptFilter;

class SetupAccountId extends Menu
{
    public static function scriptFilter()
    {
        ScriptFilter::add(
            self::accountId(),
            self::back()
        );
    }

    private static function accountId()
    {
        global $argv;

        return Item::create()
            ->title('Enter your Account ID above')
            ->subtitle('Save ' . self::userInput())
            ->arg('harvest_setup_account_id_save')
            ->variable('harvest_account_id', self::userInput())
            ->icon(Icon::create('resources/icons/harvest.png'));
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
