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
        if (! empty(Workflow::serviceEnabled())) {
            return Item::create()
                ->title('Start "' . self::userInput() . '"')
                ->arg('choose_project')
                ->variable('timer_description', self::userInput());
        }
    }

    private static function setupWorkflow()
    {
        if (empty(Workflow::serviceEnabled()) || (empty(self::userInput()))) {
            return Item::create()
                ->title('Setup the workflow')
                ->arg('setup')
                ->icon(Icon::create('resources/icons/icon.png'));
        }
    }
}
