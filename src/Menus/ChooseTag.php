<?php

namespace Godbout\Alfred\Time\Menus;

use Godbout\Alfred\Time\Workflow;
use Godbout\Alfred\Workflow\Icon;
use Godbout\Alfred\Workflow\Item;

class ChooseTag extends Menu
{
    public static function content(): array
    {
        $tags = [];

        $noTag[] = Item::create()
            ->title('No tag')
            ->subtitle('Timer will be created without a tag')
            ->arg('do')
            ->variable('timer_action', 'start');

        $serviceEnabled = Workflow::serviceEnabled();

        foreach ($serviceEnabled->tags() as $id => $name) {
            $tags[] = Item::create()
                ->title($name)
                ->subtitle($id)
                ->variable('timer_tag', $name)
                ->icon(Icon::create("resources/icons/$serviceEnabled.png"))
                ->arg('do')
                ->variable('timer_action', 'start');
        }

        return array_merge($noTag, $tags);
    }
}
