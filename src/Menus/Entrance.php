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
            self::timerAction(),
            self::setupWorkflow()
        ];
    }

    private static function timerAction()
    {
        $serviceEnabled = Workflow::serviceEnabled();

        if (! $serviceEnabled) {
            return;
        }

        if ($serviceEnabled->runningTimer()) {
            return self::stopCurrentTimer();
        }

        return self::startTimer();
    }

    private static function stopCurrentTimer()
    {
        return Item::create()
            ->title('Stop current timer')
            ->subtitle('That timer is currently running!');
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
