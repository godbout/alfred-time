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
        $service = Workflow::serviceEnabled();

        if ($service->allowsEmptyTag) {
            ScriptFilter::add(self::getNoTag());
        }

        foreach (self::getServiceTags($service) as $tag) {
            ScriptFilter::add($tag);
        }

        if (self::userInput()) {
            ScriptFilter::filterItems(self::userInput());
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
                ->variable('timer_tag_id', $id)
                ->match($name)
                ->icon(Icon::create("resources/icons/$service.png"))
                ->arg('do')
                ->variable('timer_action', 'start');
        }

        return $tags;
    }
}
