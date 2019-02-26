<?php

namespace Godbout\Alfred\Time\Menus;

use Godbout\Alfred\Time\Workflow;
use Godbout\Alfred\Workflow\Icon;
use Godbout\Alfred\Workflow\Item;

class Entrance extends Menu
{
    public static function content(): array
    {
        $items = [];

        global $argv;

        $servicesEnabled = Workflow::servicesEnabled();

        if (! empty($servicesEnabled)) {
            $items[] = Item::create()
                ->title('Start "' . trim($argv[1] ?? '') . '"')
                ->arg('setup_timer');
        }

        $items[] = Item::create()
            ->title('Setup the workflow')
            ->arg('setup')
            ->icon(Icon::create(__DIR__ . '/../../resources/icons/icon.png'));

        return $items;
    }
}
