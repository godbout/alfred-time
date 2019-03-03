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
            ->arg('no_tag');

        $serviceEnabled = Workflow::serviceEnabled();

        foreach ($serviceEnabled->tags() as $id => $name) {
            $tags[] = Item::create()
                ->title($name)
                ->variable('tag_id', $id)
                ->icon(Icon::create("resources/icons/$serviceEnabled.png"));
        }

        return array_merge($noTag, $tags);
    }
}
