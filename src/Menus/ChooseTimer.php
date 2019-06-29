<?php

namespace Godbout\Alfred\Time\Menus;

use Godbout\Alfred\Time\Workflow;
use Godbout\Alfred\Workflow\Icon;
use Godbout\Alfred\Workflow\Item;
use Godbout\Alfred\Workflow\ScriptFilter;

class ChooseTimer extends Menu
{
    public static function scriptFilter()
    {
        $service = Workflow::serviceEnabled();

        foreach (self::getServicePastTimers($service) as $pastTimer) {
            ScriptFilter::add($pastTimer);
        }

        if (self::userInput()) {
            ScriptFilter::filterItems(self::userInput());
        }
    }

    private static function getServicePastTimers($service)
    {
        $pastTimers = [];

        foreach ($service->pastTimers() as $id => $pastTimer) {
            $pastTimers[] = Item::create()
                ->title($pastTimer->description)
                ->subtitle(self::pastTimerSubtitle($pastTimer))
                ->icon(Icon::create("resources/icons/$service.png"))
                ->arg('do')
                ->variable('timer_action', 'continue')
                ->variable('timer_id', $pastTimer->id)
                ->variable('timer_description', $pastTimer->description)
                ->variable('timer_project_id', $pastTimer->project_id)
                ->variable('timer_project_name', $pastTimer->project_name)
                ->variable('timer_tag_id', $pastTimer->tag_id)
                ->variable('timer_tag', $pastTimer->tags);
        }

        return $pastTimers;
    }

    protected static function pastTimerSubtitle($pastTimer)
    {
        return "$pastTimer->project_name, [$pastTimer->tags], $pastTimer->duration";
    }
}
