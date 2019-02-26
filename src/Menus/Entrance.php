<?php

namespace Godbout\Alfred\Time\Menus;

use Godbout\Alfred\Time\Workflow;
use Godbout\Alfred\Workflow\Icon;
use Godbout\Alfred\Workflow\Item;

class Entrance extends Menu
{
    public static function content(): array
    {
        return [
            self::startTimer(),
            self::setupWorkflow()
        ];
    }

    private static function startTimer()
    {
        if (! empty(Workflow::servicesEnabled())) {
            return Item::create()
                ->title('Start "' . self::userInput() . '"')
                ->arg('setup_timer');
        }
    }

    private static function setupWorkflow()
    {
        return Item::create()
            ->title('Setup the workflow')
            ->arg('setup')
            ->icon(Icon::create(__DIR__ . '/../../resources/icons/icon.png'));
    }
}
