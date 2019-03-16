<?php

namespace Godbout\Alfred\Time\Menus;

use Godbout\Alfred\Time\Workflow;
use Godbout\Alfred\Workflow\Icon;
use Godbout\Alfred\Workflow\Item;
use Godbout\Alfred\Workflow\ScriptFilter;

class ChooseTag extends Menu
{
    public static function scriptFilter()
    {
        ScriptFilter::add(self::getNoTag());

        foreach (self::getServiceTags(Workflow::serviceEnabled()) as $tag) {
            ScriptFilter::add($tag);
        }

        $userInput = self::userInput();

        if ($userInput) {
            ScriptFilter::filterItems($userInput);
        }

        ScriptFilter::sortItems('asc', 'match');
    }

    private static function getNoTag()
    {
        return Item::create()
            ->title('No tag')
            ->subtitle('Timer will be created without a tag')
            ->match('')
            ->arg('do')
            ->variable('timer_action', 'start');
    }

    private static function getServiceTags($service)
    {
        $tags = [];

        foreach ($service->tags() as $id => $name) {
            $tags[] = Item::create()
                ->title($name)
                ->variable('timer_tag', $name)
                ->match($name)
                ->icon(Icon::create("resources/icons/$service.png"))
                ->arg('do')
                ->variable('timer_action', 'start');
        }

        return $tags;
    }
}
